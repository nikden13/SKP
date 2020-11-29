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
            'text' => 'required',
            'true_false' => 'required|boolean',
        ];
    }
}
