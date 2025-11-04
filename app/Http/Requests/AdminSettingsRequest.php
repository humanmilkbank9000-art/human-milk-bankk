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
                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'max:64',
                    'confirmed',
                    function ($attribute, $value, $fail) {
                        if ($value) {
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
                        }
                    },
                ],
                // Only require current password when changing password
                'current_password' => 'sometimes|required_with:password|string'
            ];
    }
}
