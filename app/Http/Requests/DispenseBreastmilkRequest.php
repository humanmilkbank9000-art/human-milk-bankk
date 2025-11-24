<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'sources.*.id' => 'required|integer|min:1',
            'sources.*.volume' => 'required|numeric|min:0.01',
            'dispensing_notes' => 'nullable|string|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'volume_dispensed.required' => 'Volume to dispense is required',
            'volume_dispensed.min' => 'Volume must be at least 0.01 ml',
            'milk_type.required' => 'Milk type is required',
            'milk_type.in' => 'Only pasteurized milk can be dispensed',
            'sources.required' => 'At least one source batch must be selected',
            'sources.min' => 'At least one source batch must be selected',
            'sources.*.type.required' => 'Source type is required',
            'sources.*.type.in' => 'Only pasteurized sources are allowed',
            'sources.*.id.required' => 'Source batch ID is required',
            'sources.*.id.integer' => 'Source batch ID must be a valid integer',
            'sources.*.id.min' => 'Source batch ID must be positive',
            'sources.*.volume.required' => 'Volume for each source is required',
            'sources.*.volume.min' => 'Volume must be at least 0.01 ml'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => 'Validation failed',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
