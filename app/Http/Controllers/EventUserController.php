<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Models\Event;
use App\Models\Test;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EventUserController extends Controller
{

    public function show_users_from_event(Event $event)
    {
        $user = $event->users->where('pivot.user_id', auth()->user()->id)->first();
        $isSubscribed = false;
        if ($user) {
            $isSubscribed = true;
        }
        $users = $event->users
            ->sortBy('pivot.role')
            ->values();
        foreach ($users as $user) {
            unset($user['pivot']);
        }
        return response()->json(['participants' => $users, 'isSubscribed' => $isSubscribed],200);
    }

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
        $qr_user = $request->input('code');
        $qr_event = $event->code()->find($event->id)->only('code');
        if ($qr_event['code'] == $qr_user) {
            $user->events()->updateExistingPivot($event,
                [
                    'code' => $qr_user,
                    'lock' => true,
                    'presence' => true,
                ]);
        } else {
            $user->events()->updateExistingPivot($event,
                [
                    'code' => $qr_user,
                    'lock' => true,
                ]);
        }
        return response()->json(['message' => 'Answer added successfully'], 200);
    }

    public function add_answers_user(Event $event, AnswerRequest $request)
    {
        $test = $event->test;
        $true_answers = $this->select_true_answers($test);          //получение правильных ответов
        $set_id = $this->select_id_questions($true_answers);                //получение id вопросов в тесте

        $count_qustions = count($set_id);                           //количество вопросов в тесте

        $user_answers = collect($request->input('answers'));    //ответы пользователя

        $user = auth()->user();
        foreach ($user_answers as $answer) {    //сохранение ответов
            try {
                $user->questions()->attach($answer['question_id'], ['text' => $answer['text']]);
            } catch (QueryException $e) {
                return response()->json(['message' => 'Not found question'], 400);
            }
        }

        $count_true_answers = $this->check_answers($set_id, $true_answers, $user_answers); //проверка ответов
        if ($count_true_answers >= $count_qustions * 0.8) {
            $user->events()->updateExistingPivot($event,
                [
                    'presence' => true,
                    'lock' => true,
                ]);
        } else {
            $user->events()->updateExistingPivot($event,
                [
                    'lock' => true,
                ]);
        }
        return response()->json(['message' => 'Answers added successfully'], 200);
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

    private function check_answers($set_id, $true_answers, $user_answers)
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
            foreach ($trueAnswers_for_question as $item) {
                $trueAnswers_text->push((string)$item['text']);
            }
            foreach ($userAnswers_for_question as $item) {
                $userAnswers_text->push((string)$item['text']);
            }
            foreach ($trueAnswers_text as $item) {
                if ($userAnswers_text->contains($item)) {
                    $userAnswers_text->forget($userAnswers_text->search($item));
                    $userAnswers_text = $userAnswers_text->values();
                    continue;
                }
                break;
            }
            if ($userAnswers_text->isEmpty()) {
                $count_true_answers++;
            }
        }
        return $count_true_answers;
    }
}
