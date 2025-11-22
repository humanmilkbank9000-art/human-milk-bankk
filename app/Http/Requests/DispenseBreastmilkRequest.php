<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DispenseBreastmilkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'volume_dispensed' => 'required|numeric|min:0.01',
            'milk_type' => 'required|in:pasteurized',
            'sources' => 'required|array|min:1',
            'sources.*.type' => 'required|in:pasteurized',
            'sources.*.id' => 'required|integer',
            'sources.*.volume' => 'required|numeric|min:0.01',
            'dispensing_notes' => 'nullable|string|max:1000'
        ];
    }
}
