<?php

namespace App\Http\Requests\Linear;

use App\Rules\Total100;
use Illuminate\Foundation\Http\FormRequest;

class CalculateComparison extends FormRequest
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
            'prize_unit' => 'required|integer|min:0|max:8192',
            'potential_participants' => 'required|integer|min:1|max:10000',
            'participation_fee' => 'required|integer|min:0|max:1000000',
            'bankers_budget' => 'required|integer|min:1|max:33554432',
            'cognitive_degrees_distribution' => ['required', 'array', new Total100()]
        ];
    }

}
