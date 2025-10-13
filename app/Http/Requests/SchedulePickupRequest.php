<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchedulePickupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'scheduled_pickup_date' => 'required|date|after_or_equal:today',
            'scheduled_pickup_time' => 'required|date_format:H:i'
        ];
    }
}
