<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax() || $this->wantsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422)
            );
        }
        parent::failedValidation($validator);
    }

    public function rules()
    {
        $rules = [
            'donation_method' => 'required|in:walk_in,home_collection',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];

        // Walk-in validation
        if ($this->input('donation_method') === 'walk_in') {
            $rules['availability_id'] = 'required|exists:admin_availability,id';
        }

        // Home collection validation
        if ($this->input('donation_method') === 'home_collection') {
            $rules['first_expression_date'] = 'required|date';
            $rules['last_expression_date'] = 'required|date|after_or_equal:first_expression_date';
            $rules['bag_time'] = 'required|array|min:1';
            $rules['bag_time.*'] = 'required';
            $rules['bag_date'] = 'required|array|min:1';
            $rules['bag_date.*'] = 'required|date';
            $rules['bag_number'] = 'required|array|min:1';
            $rules['bag_number.*'] = 'required|integer';
            $rules['bag_volume'] = 'required|array|min:1';
            $rules['bag_volume.*'] = 'required|numeric|min:0.01';
            $rules['bag_storage'] = 'required|array|min:1';
            $rules['bag_storage.*'] = 'required|in:REF,FRZ';
            $rules['bag_temp'] = 'required|array|min:1';
            $rules['bag_temp.*'] = 'required|numeric';
            $rules['bag_method'] = 'required|array|min:1';
            $rules['bag_method.*'] = 'required|string';

            // Lifestyle checklist (optional); captured client-side and sent as YES/NO
            foreach ([
                'good_health','no_smoking','no_medication','no_alcohol','no_fever',
                'no_cough_colds','no_breast_infection','followed_hygiene','followed_labeling','followed_storage'
            ] as $field) {
                $rules[$field] = 'nullable|in:YES,NO';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'first_expression_date.required' => 'Please provide the first expression date.',
            'last_expression_date.required' => 'Please provide the last expression date.',
            'last_expression_date.after_or_equal' => 'Last expression date must be on or after the first expression date.',
            'bag_time.required' => 'Please provide time for all bags.',
            'bag_date.required' => 'Please provide date for all bags.',
            'bag_volume.required' => 'Please provide volume for all bags.',
            'bag_volume.*.min' => 'Bag volume must be greater than 0.',
            'bag_storage.required' => 'Please select storage location for all bags.',
            'bag_temp.required' => 'Please provide temperature for all bags.',
            'bag_method.required' => 'Please select collection method for all bags.',
            // No extra messages required for client-side consent
        ];
    }
}
