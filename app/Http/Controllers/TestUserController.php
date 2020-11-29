<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TestUserController extends Controller
{

    public function add_user(Test $test)
    {
        $user_tests = auth()->user()->tests;
        foreach ($user_tests as $user_test) {
            if ($user_test->pivot->role == 'participant' && $user_test->pivot->test_id == $test->id) {
                return response()->json(['message' => 'You are already a participant in this test'],400);
            }
        }
        auth()->user()->tests()->attach($test);
        return response()->json(['message' => 'You have been added to the test'],200);
    }



    public function add_answers_user(Test $test, Request $request)
    {

        $true_answers = $this->select_true_answers($test);          //получение правильных ответов
        $set_id = $this->select_id_questions($true_answers);                //получение id вопросов в тесте

        $count_qustions = count($set_id);                           //количество вопросов в тесте

        $user_answers = collect($request->input('answers'));    //ответы пользователя

        $user = auth()->user();
        $user_tests = $user->tests;
        foreach ($user_tests as $user_test) {
            if ($user_test->pivot->role == 'participant' && $user_test->pivot->test_id == $test->id) {  //является ли участником теста

                $count_true_answers = $this->check_answers($set_id, $true_answers, $user_answers, $count_qustions); //проверка ответов
                if ($count_true_answers >= $count_qustions * 0.8)
                    $user->tests()->updateExistingPivot($test, ['presence' => true]);

                foreach ($user_answers as $answer) {    //сохранение ответов
                    try {
                        $user->questions()->attach($answer['question_id'], ['text' => $answer['text']]);
                    } catch (QueryException $e) {
                        return response()->json(['message' => 'Not found question'], 400);
                    }
                }
                return response()->json(['message' => 'Answers added successfully'], 200);
            }
        }
        return response()->json(['message' => 'You are not a participant in this test'],400);
    }

    public function show_users_from_test(Test $test)
    {
        $users = $test->users
            ->where('pivot.role', 'participant')
            ->values();
        $array=['id' => []];
        foreach ($users as $user) {
            array_push($array['id'], $user['id']);
            //unset($user['pivot']);
        }
        return response()->json($array,200);
    }

    private function select_true_answers(Test $test)
    {
        $true_answers = collect([]);
        $questions = $test->questions;
        foreach ($questions as $question) {
            $answers = $question->answers->where('true_false', true);
            foreach ($answers as $answer) {
                $true_answers->push($answer);
            }
        }
        return $true_answers;
    }
    private function select_id_questions($true_answers)
    {
        $set_id = [];
        foreach ($true_answers as $true_answer) {
            array_push($set_id, $true_answer->question_id);
            $set_id = array_unique($set_id);
        }
        return $set_id;
    }

    private function check_answers($set_id, $true_answers, $user_answers, $count_qustions)
    {
        $count_true_answers = 0;
        foreach ($set_id as $id) {
            $trueAnswers_for_question = $true_answers->where('question_id', $id);
            $userAnswers_for_question = $user_answers->where('question_id', $id);
            if ($trueAnswers_for_question->count() != $userAnswers_for_question->count()) {
                continue;
            }
            $trueAnswers_text = collect([]);
            $userAnswers_text = collect([]);
            try {
                foreach ($trueAnswers_for_question as $item) {
                    $trueAnswers_text->push((string)$item['text']);
                }
                foreach ($userAnswers_for_question as $item) {
                    $userAnswers_text->push((string)$item['text']);
                }
            } catch(Exception $e) {
                return response()->json(['message' => 'Missing text field'], 400);
            }
            foreach ($trueAnswers_text as $item) {
                if ($userAnswers_text->contains($item)) {
                    $userAnswers_text->forget($userAnswers_text->search($item));
                    $userAnswers_text = $userAnswers_text->values();
                    continue;
                }
                break;
            }
            if ($userAnswers_text->isEmpty())
                $count_true_answers++;
        }
        return $count_true_answers;
    }

}
