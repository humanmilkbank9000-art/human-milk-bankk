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
                'password'       => [
                    'required',
                    'confirmed',
                    'string',
                    'min:8',
                    'max:64',
                    function ($attribute, $value, $fail) {
                        if (!preg_match('/[A-Z]/', $value)) {
                            $fail('Password must contain at least one uppercase letter.');
                        }
                        if (!preg_match('/[a-z]/', $value)) {
                            $fail('Password must contain at least one lowercase letter.');
                        }
                        if (!preg_match('/[0-9]/', $value)) {
                            $fail('Password must contain at least one number.');
                        }
                        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
                            $fail('Password must contain at least one special character.');
                        }
                    },
                ],
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
