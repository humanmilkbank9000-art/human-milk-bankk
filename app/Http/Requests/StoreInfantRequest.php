<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInfantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name'            => 'required|string',
            'middle_name'           => 'nullable|string',
            'last_name'             => 'required|string',
            'suffix'                => 'nullable|string',
            'infant_sex'            => 'required|in:female,male',
            'infant_date_of_birth'  => 'required|date',
            'birth_weight'          => 'required|numeric|min:0',
        ];
    }
}
