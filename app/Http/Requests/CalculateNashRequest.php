<?php

namespace App\Http\Requests;

use App\Rules\Coordinate;
use App\Rules\FractionMax;
use Illuminate\Foundation\Http\FormRequest;

class CalculateNashRequest extends FormRequest
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
            'alpha_1.numerator' => 'required|integer|min:1|max:1000',
            'alpha_1.denominator' => 'required|integer|min:1|max:1000',
            'alpha_2.numerator' => 'required|integer|min:1|max:1000',
            'alpha_2.denominator' => 'required|integer|min:1|max:1000',
            'beta_1.numerator' => 'required|integer|min:1|max:1000',
            'beta_1.denominator' => 'required|integer|min:1|max:1000',
            'beta_2.numerator' => 'required|integer|min:1|max:1000',
            'beta_2.denominator' => 'required|integer|min:1|max:1000',
            'alpha_1' => ['required', 'array', new FractionMax(1)],
            'alpha_2' => ['required', 'array', new FractionMax(1)],
            'beta_1' => ['required', 'array', new FractionMax(1)],
            'beta_2' => [
                'required',
                'array',
                new FractionMax(1),
                new Coordinate(
                    $this->input('alpha_1'),
                    $this->input('alpha_2'),
                    $this->input('beta_1'),
                    $this->input('beta_2')
                )
            ],
        ];
    }

    public function attributes()
    {
        return [
            'alpha_1.numerator' => trans('validation.attributes.alpha_1_numerator'),
            'alpha_1.denominator' => trans('validation.attributes.alpha_1_denominator'),
            'alpha_2.numerator' => trans('validation.attributes.alpha_2_numerator'),
            'alpha_2.denominator' => trans('validation.attributes.alpha_2_denominator'),
            'beta_1.numerator' => trans('validation.attributes.beta_1_numerator'),
            'beta_1.denominator' => trans('validation.attributes.beta_1_denominator'),
            'beta_2.numerator' => trans('validation.attributes.beta_2_numerator'),
            'beta_2.denominator' => trans('validation.attributes.beta_2_denominator'),
        ];
    }
}
