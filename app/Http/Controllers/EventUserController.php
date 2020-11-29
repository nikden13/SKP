<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventUserController extends Controller
{

    public function add_user(Event $event)
    {
        $user_events = auth()->user()->events;
        foreach ($user_events as $user_event) {
            if ($user_event->pivot->role == 'participant' && $user_event->pivot->event_id == $event->id) {
                return response()->json(['message' => 'You are already a participant in this event'],400);
            }
        }
        auth()->user()->events()->attach($event);
        return response()->json(['message' => 'You have been added to the event'],200);
    }

    public function add_qr_code_user(Event $event, Request $request)
    {
        $user = auth()->user();
        $user_events = $user->events;
        $qr = $request->input('qr_code');
        foreach ($user_events as $user_event) {
            if ($user_event->pivot->role == 'participant' && $user_event->pivot->event_id == $event->id) {
                $qr_event = Event::find($event->id)->only('qr_code');

                if ($qr_event['qr_code']  == $qr) {
                    $user->events()->updateExistingPivot($event, ['qr_code' => $qr, 'presence' => true]);
                } else {
                    $user->events()->updateExistingPivot($event, ['qr_code' => $qr, 'presence' => false]);
                }
                return response()->json(['message' => 'QR-code added successfully'], 200);
            }
        }
        return response()->json(['message' => 'You are not a participant in this event'],400);
    }

    public function show_users_from_event(Event $event)
    {
        $users = $event->users
            ->where('pivot.role', 'participant')
            ->values();
        foreach ($users as $user) {
            unset($user['pivot']);
        }
        return response()->json($users,200);
    }

}
