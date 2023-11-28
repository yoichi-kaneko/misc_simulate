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
            'patterns.*.base_numerator' => 'required|integer|min:1|max:2000',
            'patterns.*.numerator_exp_1' => 'required|integer|min:1|max:5',
            'patterns.*.numerator_exp_2' => 'required|integer|min:1|max:5',
            'patterns.*.denominator_exp' => 'required|integer|min:0|max:12',
            'max_step' => 'required|integer|min:1|max:200',
            'max_rc' => 'nullable|integer|min:1|max:200',
            'combination_player_1' => 'nullable|array',
            'combination_player_1.a' => 'nullable|regex:/^[12]$/',
            'combination_player_1.b' => 'nullable|regex:/^[12]$/',
        ];
    }

    public function attributes()
    {
        return [
            'patterns.*.base_numerator' => trans('validation.attributes.base_numerator'),
            'patterns.*.numerator_exp_1' => trans('validation.attributes.numerator_exp_1'),
            'patterns.*.numerator_exp_2' => trans('validation.attributes.numerator_exp_2'),
            'patterns.*.denominator_exp' => trans('validation.attributes.denominator_exp'),
        ];
    }

}
