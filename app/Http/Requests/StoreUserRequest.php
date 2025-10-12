<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name'     => 'required|string',
            'middle_name'    => 'nullable|string',
            'last_name'      => 'required|string',
            'contact_number' => 'required|max:11|unique:user,contact_number',
            'password'       => 'required|confirmed',
            'address'        => 'required|string',
            'date_of_birth'  => 'required|date',
            'sex'            => 'required|in:female,male',
        ];
    }

    public function messages()
    {
        return [
            'contact_number.unique' => 'This contact number is already registered. Please use a different number.',
        ];
    }
}
