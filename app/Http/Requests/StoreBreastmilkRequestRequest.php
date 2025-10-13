<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBreastmilkRequestRequest extends FormRequest
{
    public function authorize()
    {
        // Authorization (session-based) is handled in controller; allow validation here
        return true;
    }

    public function rules()
    {
        return [
            'infant_id' => 'required|exists:infant,infant_id',
            'availability_id' => 'required|exists:admin_availability,id',
            'prescription' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ];
    }
}
