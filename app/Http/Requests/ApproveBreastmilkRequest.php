<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveBreastmilkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'volume_requested' => 'required|numeric|min:0.01',
            'milk_type' => 'required|in:pasteurized',
            'admin_notes' => 'nullable|string|max:1000',
            'selected_items' => 'required|array|min:1',
            'selected_items.*.id' => 'required|integer',
            'selected_items.*.volume' => 'required|numeric|min:0.01'
        ];
    }
}
