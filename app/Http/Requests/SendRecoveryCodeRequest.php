<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendRecoveryCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contact_number' => ['required', 'string', 'regex:/^0\d{10}$/'],
        ];
    }

    public function messages()
    {
        return [
            'contact_number.regex' => 'Please enter a valid 11-digit Philippine mobile number starting with 0.',
        ];
    }
}
