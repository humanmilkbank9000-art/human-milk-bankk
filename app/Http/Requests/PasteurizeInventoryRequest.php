<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasteurizeInventoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'donation_ids' => 'required|array|min:1',
            'donation_ids.*' => 'exists:breastmilk_donation,breastmilk_donation_id',
            'notes' => 'nullable|string|max:1000'
        ];
    }
}
