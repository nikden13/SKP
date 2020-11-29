<?php

namespace App\Http\Requests;

class EventRequest extends ApiFormRequest
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
            'type_event' => 'required|in:lecture,lab,coursework,standard_practice,educational_practice',
            'date' => 'required|date_format:Y/m/d|after:yesterday',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'check_type' => 'required|in:qr,captcha,test',
            'code' => 'required_if:check_type,qr,captcha',
            'test' => 'required_if:check_type,test',
            'test.name' => 'required_if:check_type,test',
            'test.time_limit' => 'integer|gte:0',
            'test.questions' => 'required_if:check_type,test|array',
            'test.questions.*.text' => 'required_if:check_type,test',
            'test.questions.*.answers' => 'required_if:check_type,test|array',
            'test.questions.*.answers.*.text'=> 'required_if:check_type,test',
            'test.questions.*.answers.*.true_false'=> 'required_if:check_type,test|boolean',
        ];
    }
}
