<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateSingleSpGame extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'participant_number' => 'required|integer|min:1|max:10000',
            'banker_prepared_change' => 'required|integer|min:0|max:2097152',
            'participation_fee' => 'required|integer|min:0|max:1000000',
            'banker_budget_degree' => 'required|integer|min:1|max:50',
            'initial_setup_cost' => 'required|integer|min:1|max:1000000',
            'facility_unit_cost' => 'required|integer|min:1|max:1000000',
            'facility_unit' => 'required|integer|min:1|max:1000',
            'random_seed' => 'nullable|integer|min:0',
        ];
    }

}
