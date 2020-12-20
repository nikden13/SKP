<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Filters\EventFilter;

class EventController extends Controller
{

    public function index(Request $request)
    {
        $builder = Event::all()->sortBy($request->input('sort_by'));
        $events = (new EventFilter($builder, $request))->apply();
        $events_with_creator = [];
        foreach ($events as $event) {
            $creator = $event->users
                ->where('pivot.role', 'creator')
                ->first()
                ->only(['name', 'surname', 'patronymic']);
            array_push($events_with_creator, collect($event)
                ->except('users')
                ->union(['creator' => $creator]));
        }
        return response()->json($events_with_creator, 200);
    }

    public function show(Event $event)
    {
        $creator = $event->users
            ->where('pivot.role', 'creator')
            ->first()
            ->only(['name', 'surname', 'patronymic']);
        $event = collect($event)->except('users');
        return response()->json($event->union(['creator' => $creator]), 200);
    }

    public function store(EventRequest $request)
    {
        $event = Event::create($request->all());
        if ($request->input('check_type') != 'test') {
            $this->create_code($event, $request);
        } else {
            $this->create_test($event, $request);
        }
        auth()->user()->events()->attach($event, ['role' => 'creator', 'presence' => true]);
        return response()->json(Event::find($event->id), 201);
    }

    public function update(Event $event, EventRequest $request)
    {
        //обнуление результатов
        foreach ($event->users as $user) {
            $event->users()->updateExistingPivot($user,
                [
                    'code' => null,
                    'presence' => false,
                    'lock' => false,
                ]);
        }
        //обновление
        if ($event->check_type != 'test' && $request->input('check_type') == 'test') {
            $event->update($request->all());
            $event->code()->delete();
            $this->create_test($event, $request);
        } elseif ($event->check_type == 'test' && $request->input('check_type') != 'test') {
            $event->update($request->all());
            $event->test()->delete();
            $this->create_code($event, $request);
        } elseif ($event->check_type == 'test' && $request->input('check_type') == 'test') {
            $event->update($request->all());
            $event->test()->delete();
            $this->create_test($event, $request);
        } else {
            $event->update($request->all());
            $event->code()->delete();
            $this->create_code($event, $request);
        }
        return response()->json(Event::find($event->id),200);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json('', 204);
    }

    private function create_code($event, $request)
    {
        $event->code()->create(['code' => $request->input('code')]);
    }

    public function getEventsRoot(Request $request)
    {
        $events = auth()->user()->events->sortBy('date');
        $events_with_code = [];
        foreach ($events as $event) {
            if ($event->code) {
                array_push($events_with_code, collect($event)
                ->except('pivot')
                ->union(['code' => $event->code->code]));
            }
            else {
                array_push($events_with_code, collect($event)
                    ->except('pivot')
                    ->except('code')
                );
            }
        }
        return response()->json($events_with_code, 200);
    }

    private function create_test($event, $request)
    {
        $test = $event->test()->create([
            'name' => $request->input('test.name'),
            'time_limit' => $request->input('test.time_limit'),
        ]);
        foreach ($request->input('test.questions') as $question) {
            $save_question = $test->questions()->create($question);
            foreach ($question['answers'] as $answer) {
                $save_question->answers()->create($answer);
            }
        }
    }

}
