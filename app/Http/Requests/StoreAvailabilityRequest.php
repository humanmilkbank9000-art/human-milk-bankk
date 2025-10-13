<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilityRequest extends FormRequest
{
    public function authorize()
    {
        // Keep authorization simple for now; adjust to your app auth logic as needed
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|array|min:1',
            'time.*' => 'required|date_format:H:i',
        ];
    }
}
