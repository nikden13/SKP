<?php

namespace App\Http\Controllers;

use App\Models\Event;

class VisitedController extends Controller
{

    public function __invoke(Event $event)
    {
        $users = $event->users
            ->where('pivot.role', 'participant')
            ->sortBy('second_name');
        $visitors = [];
        foreach ($users as $user) {
            $visitor = array_merge(
                $user->only('id', 'second_name', 'first_name', 'middle_name'),
                ["presence" => $user->pivot->presence]
            );
            array_push($visitors, $visitor);
        }
        return response()->json(['visitors' => $visitors],200);
    }

}
