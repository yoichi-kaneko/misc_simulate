<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateCentipedeRequest extends FormRequest
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
            'base_numerator' => 'required|integer|min:100|max:700',
            'numerator_exp_1' => 'required|integer|min:1|max:5',
            'numerator_exp_2' => 'required|integer|min:1|max:5',
            'denominator_exp' => 'required|integer|min:0|max:12',
            'chart_offset' => 'required|integer|min:1|max:147',
        ];
    }

}
