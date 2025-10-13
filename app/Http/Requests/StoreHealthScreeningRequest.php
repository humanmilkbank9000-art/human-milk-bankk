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

        $requiredYesNo = [
            'medical_history_01', 'medical_history_03', 'medical_history_07',
            'medical_history_09', 'medical_history_14', 'medical_history_15',
            'sexual_history_01', 'sexual_history_02', 'sexual_history_04',
            'donor_infant_01', 'donor_infant_02', 'donor_infant_03'
        ];

        foreach ($requiredYesNo as $field) {
            $rules[$field] = 'required|in:yes,no';
        }

        $optionalFields = [
            'medical_history_02', 'medical_history_04', 'medical_history_05',
            'medical_history_06', 'medical_history_08', 'medical_history_10',
            'medical_history_11', 'medical_history_12', 'medical_history_13',
            'sexual_history_03', 'donor_infant_04', 'donor_infant_05'
        ];

        foreach ($optionalFields as $field) {
            $rules[$field] = 'nullable|string';
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
