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
        $event = (new EventFilter($builder, $request))->apply();
        return response()->json($event->values()->all(), 200);
    }

    public function show(Event $event)
    {
        $creator = $event->users
            ->where('pivot.role', 'creator')
            ->first()
            ->only(['first_name', 'second_name', 'middle_name']);
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
        auth()->user()->events()->attach($event, ['role' => 'creator']);
        return response()->json(Event::find($event->id), 201);
    }

    public function update(Event $event, EventRequest $request)
    {
        $event->update($request->all());
        if ($event->check_tipe != 'test' && $request->input('check_type') == 'test') {
            $event->code()->delete();
            $this->create_test($event, $request);
        } elseif ($event->check_tipe == 'test' && $request->input('check_type') != 'test') {
            $event->test()->delete();
            $this->create_code($event, $request);
        } elseif ($event->check_tipe == 'test' && $request->input('check_type') == 'test') {
            $this->update_test($event, $request);
        } else {
            $this->update_code($event, $request);
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
        $event->code()->create($request->all());
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

    private function update_code($event, $request)
    {
        $event->code()->update($request->all());
    }

    private function update_test($event, $request)
    {
        $test = $event->test()->update([
            'name' => $request->input('test.name'),
            'time_limit' => $request->input('test.time_limit'),
        ]);
        foreach ($request->input('test.questions') as $question) {
            $save_question = $test->questions()->update($question);
            foreach ($question['answers'] as $answer) {
                $save_question->answers()->update($answer);
            }
        }
    }

}
