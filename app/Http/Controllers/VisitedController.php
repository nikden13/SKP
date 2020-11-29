<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Test;

class VisitedController extends Controller
{
    public $users = [];

    public function visitors_event(Event $event)
    {
        $this->users = $event->users->sortBy('second_name');
        return $this->visitors();
    }

    public function visitors_test(Test $test)
    {
        $this->users = $test->users->sortBy('second_name');
        return $this->visitors();
    }

    private function visitors()
    {
        $visitors = [];
        foreach ($this->users as $user) {
            if ($user->pivot->role == 'participant') {
                $visitor = array_merge($user->only('second_name', 'first_name', 'middle_name'), ["presence" => $user->pivot->presence]);
                array_push($visitors, $visitor);
            }
        }
        return response()->json(['visitors' => $visitors],200);
    }

}
