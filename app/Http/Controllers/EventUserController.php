<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EventUserController extends Controller
{

    public function add_user(Event $event)
    {
        try {
            auth()->user()->events()->attach($event);
        } catch (QueryException $e) {
            return response()->json(['message' => 'You are already a participant in this event'],400);
        }
        return response()->json(['message' => 'You have been added to the event'],200);
    }

    public function add_code_user(Event $event, Request $request)
    {
        $user = auth()->user();
        $user_event = $user->events->where('pivot.event_id', $event->id)->first();
        if ($user_event) {
            $qr_user = $request->input('code');
            $qr_event = $event->code()->find($event->id)->only('code');
            if ($qr_event['code'] == $qr_user) {
                $user->events()->updateExistingPivot($event, ['code' => $qr_user, 'presence' => true]);
            } else {
                $user->events()->updateExistingPivot($event, ['code' => $qr_user, 'presence' => false]);
            }
            return response()->json(['message' => 'Answer added successfully'], 200);
        }
        return response()->json(['message' => 'You are not a participant in this event'],400);
    }

    public function show_users_from_event(Event $event)
    {
        $users = $event->users;
        foreach ($users as $user) {
            unset($user['pivot']);
        }
        return response()->json($users,200);
    }

}
