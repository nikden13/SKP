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
            'qr_code' => 'required',
        ];
    }
}
