<?php

namespace App\Http\Controllers;

use App\Models\Event;

class TestController extends Controller
{

    public function show(Event $event)
    {
        $test = $event->test;
        if (!$test) {
            return response()->json(['message' => 'Test not exists'], 200);
        }
        $questions = $test->questions;
        foreach ($questions as $question) {
            $question->answers;
        }
        return response()->json($test, 200);
    }

}
