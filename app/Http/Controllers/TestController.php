<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestRequest;
use App\Models\Event;
use App\Models\Test;
use Illuminate\Database\QueryException;

class TestController extends Controller
{

    /*public function index(Request $request)
    {
        $builder = Test::all()->sortBy($request->input('sort_by'));
        $test = (new TestFilter($builder, $request))->apply();
        return response()->json($test->values()->all(), 200);
    }*/

   /* public function store(Event $event, TestRequest $request)
    {
        $test = $event->test()->create($request->all());
        foreach ($request->input('questions') as $question) {
            $save_question = $test->questions()->create($question);
            foreach ($question['answers'] as $answer) {
                $save_question->answers()->create($answer);
            }
        }
        return response()->json(Test::find($test->id), 201);
    }*/

    public function show(Test $test)
    {
        $questions = $test->questions;
        foreach ($questions as $question) {
            $question->answers;
        }
        return response()->json($test, 200);
    }

    /*public function update(Test $test, TestRequest $request)
    {
        $test->update($request->all());
        return response()->json(Test::find($test->id),200);
    }*/

    /*public function destroy(Test $test)
    {
        $test->delete();
        return response()->json('', 204);
    }*/
}
