<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'current_password' => 'required|string'
        ];
    }
}
