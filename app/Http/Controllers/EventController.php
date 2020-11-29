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
        return response()->json($event, 200);
    }

    public function store(EventRequest $request)
    {
        $event = Event::create($request->all());
        auth()->user()->events()->attach($event, ['role' => 'creator']);
        return response()->json(Event::find($event->id), 201);
    }

    public function update(Event $event, EventRequest $request)
    {
        $event->update($request->all());
        return response()->json(Event::find($event->id),200);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json('', 204);
    }

}
