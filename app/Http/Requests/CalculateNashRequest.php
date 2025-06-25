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
            'rho.numerator' => 'required|integer|min:1|max:1000',
            'rho.denominator' => 'required|integer|min:1|max:1000',
            'alpha_1' => [
                'bail',
                'required',
                'array',
                new FractionMax(1),
            ],
            'alpha_2' => [
                'bail',
                'required',
                'array',
                new FractionMax(1),
            ],
            'beta_1' => [
                'bail',
                'required',
                'array',
                new FractionMax(1),
            ],
            'beta_2' => [
                'bail',
                'required',
                'array',
                new FractionMax(1),
            ],
            'rho' => [
                'bail',
                'required',
                'array',
                new FractionMax(1),
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 最初のバリデーションが成功した場合のみ、Coordinateルールを適用
            if (! $validator->errors()->has('alpha_1') &&
                ! $validator->errors()->has('alpha_2') &&
                ! $validator->errors()->has('beta_1') &&
                ! $validator->errors()->has('beta_2')) {

                // すべての入力が配列であることを確認
                $alpha_1 = $this->input('alpha_1');
                $alpha_2 = $this->input('alpha_2');
                $beta_1 = $this->input('beta_1');
                $beta_2 = $this->input('beta_2');

                if (is_array($alpha_1) && is_array($alpha_2) && is_array($beta_1) && is_array($beta_2)) {
                    // 配列の場合のみCoordinateルールを適用
                    $coordinateRule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);

                    if (! $coordinateRule->passes('beta_2', $beta_2)) {
                        $validator->errors()->add('beta_2', $coordinateRule->message());
                    }
                }
            }
        });
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
            'rho.numerator' => trans('validation.attributes.rho_numerator'),
            'rho.denominator' => trans('validation.attributes.rho_denominator'),
        ];
    }
}
