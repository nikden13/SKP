<?php

namespace App\Http\Requests;

class TestRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'time_limit' => 'integer|gte:0',
            'questions' => 'required|array',
            'questions.*.text' => 'required',
            'questions.*.answers' => 'required|array',
            'questions.*.answers.*.text'=> 'required',
            'questions.*.answers.*.true_false'=> 'required|boolean',
        ];
    }
}
