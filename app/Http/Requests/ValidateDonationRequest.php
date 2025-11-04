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
            ,
            // Optional editable bag-level fields that admin may change during validation
            'bag_time' => 'sometimes|array',
            'bag_time.*' => 'nullable|string',
            'bag_date' => 'sometimes|array',
            'bag_date.*' => 'nullable|string',
            'bag_number' => 'sometimes|array',
            'bag_number.*' => 'nullable|integer',
            'bag_storage' => 'sometimes|array',
            'bag_storage.*' => 'nullable|string',
            'bag_temp' => 'sometimes|array',
            'bag_temp.*' => 'nullable|string',
            'bag_method' => 'sometimes|array',
            'bag_method.*' => 'nullable|string'
        ];
    }
}
