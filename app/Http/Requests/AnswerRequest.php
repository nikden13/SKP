<?php

namespace App\Http\Requests;


class AnswerRequest extends ApiFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|gte:0',
            'answers.*.text' => 'required',
        ];
    }
}
