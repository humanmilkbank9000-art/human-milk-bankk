<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|string',
            'password' => 'required|string'
        ];
    }

    /**
     * Prepare and sanitize input before validation.
     * This trims input, strips HTML tags and removes null bytes.
     */
    protected function prepareForValidation()
    {
        $phone = $this->input('phone');
        $password = $this->input('password');

        if (is_string($phone)) {
            // Remove HTML tags, trim whitespace and remove control characters
            $phone = strip_tags($phone);
            $phone = trim($phone);
            // Remove null bytes just in case
            $phone = str_replace("\0", '', $phone);
        }

        if (is_string($password)) {
            // Trim password (do not strip tags to avoid altering intended characters too aggressively)
            $password = trim($password);
        }

        $this->merge([
            'phone' => $phone,
            'password' => $password,
        ]);
    }
}
