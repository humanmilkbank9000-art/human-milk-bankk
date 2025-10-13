<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateDonationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number_of_bags' => 'required|integer|min:1',
            'bag_volumes' => 'required|array|min:1',
            'bag_volumes.*' => 'required|numeric|min:0.01'
        ];
    }
}
