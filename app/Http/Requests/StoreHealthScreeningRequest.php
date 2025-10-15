<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthScreeningRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'civil_status'   => 'required|in:single,married,divorced,widowed',
            'occupation'     => 'required|string',
            'type_of_donor'  => 'required|in:community,private,employee,network_office_agency',
        ];

        // All medical history questions (15 questions)
        for ($i = 1; $i <= 15; $i++) {
            $field = 'medical_history_' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $rules[$field] = 'required|in:yes,no';
        }

        // All sexual history questions (4 questions)
        for ($i = 1; $i <= 4; $i++) {
            $field = 'sexual_history_' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $rules[$field] = 'required|in:yes,no';
        }

        // All donor infant questions (5 questions)
        for ($i = 1; $i <= 5; $i++) {
            $field = 'donor_infant_' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $rules[$field] = 'required|in:yes,no';
        }

        // Conditional details fields (allow text when user specifies)
        $detailsFields = [
            'medical_history_02_details', 'medical_history_04_details', 'medical_history_05_details',
            'medical_history_08_details', 'medical_history_10_details', 'medical_history_11_details', 'medical_history_13_details',
            'sexual_history_03_details', 'sexual_history_04_details',
            'donor_infant_04_details', 'donor_infant_05_details'
        ];

        foreach ($detailsFields as $field) {
            $rules[$field] = 'nullable|string|max:1000';
        }

        // infant_id may be provided from the request
        $rules['infant_id'] = 'nullable|exists:infant,infant_id';

        return $rules;
    }
}
