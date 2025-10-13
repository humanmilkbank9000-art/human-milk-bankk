<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'donation_method' => 'required|in:walk_in,home_collection',
            // Walk-in fields
            'availability_id' => 'nullable|exists:admin_availability,id',
            // Home collection fields
            'number_of_bags' => 'nullable|integer|min:1',
            'bag_volumes' => 'nullable|array|min:1',
            'bag_volumes.*' => 'nullable|numeric|min:0.01',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }
}
