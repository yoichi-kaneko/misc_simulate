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
            'pattern.*.base_numerator' => 'required|integer|min:100|max:700',
            'pattern.*.numerator_exp_1' => 'required|integer|min:1|max:5',
            'pattern.*.numerator_exp_2' => 'required|integer|min:1|max:5',
            'pattern.*.denominator_exp' => 'required|integer|min:0|max:12',
            'max_step' => 'required|integer|min:50|max:200',
            'chart_offset' => 'required|integer|min:1|lt:max_step',
        ];
    }

    public function attributes()
    {
        return [
            'pattern.*.base_numerator' => trans('validation.attributes.base_numerator'),
            'pattern.*.numerator_exp_1' => trans('validation.attributes.numerator_exp_1'),
            'pattern.*.numerator_exp_2' => trans('validation.attributes.numerator_exp_2'),
            'pattern.*.denominator_exp' => trans('validation.attributes.denominator_exp'),
        ];
    }

}
