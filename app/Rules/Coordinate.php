<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Coordinate implements ValidationRule
{
    private $alpha_1;
    private $alpha_2;
    private $beta_1;
    private $beta_2;

    // For backward compatibility
    private $validationFailed = false;

    /**
     * @param array $alpha_1
     * @param array $alpha_2
     * @param array $beta_1
     * @param array $beta_2
     */
    public function __construct(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2
    ) {
        $this->alpha_1 = $alpha_1;
        $this->alpha_2 = $alpha_2;
        $this->beta_1 = $beta_1;
        $this->beta_2 = $beta_2;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /*
         * [alpha_1, alpha_2]
         * [beta_1, beta_2]
         * という[x,y]座標を持つ2点をセットし、
         * 1つ目の座標が2つ目の座標の右下にあるかを判定する。
         * （x,yの座標が同じ場合はfalse）
         *
         * 4変数に基づく判定処理のため、$valueには値をセットせず、すべてコンストラクタで渡す
         */
        $alpha_1 = $this->alpha_1['numerator'] / $this->alpha_1['denominator'];
        $alpha_2 = $this->alpha_2['numerator'] / $this->alpha_2['denominator'];
        $beta_1 = $this->beta_1['numerator'] / $this->beta_1['denominator'];
        $beta_2 = $this->beta_2['numerator'] / $this->beta_2['denominator'];

        // 右下にあるかを判定
        if (! ($alpha_1 > $beta_1 && $alpha_2 < $beta_2)) {
            $this->validationFailed = true;
            $fail(trans('validation.invalid_coordinate'));
        } else {
            $this->validationFailed = false;
        }
    }

    /**
     * For backward compatibility with the old Rule interface
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->validate($attribute, $value, function ($message) {
            // Do nothing, just capture the failure
        });

        return ! $this->validationFailed;
    }

    /**
     * For backward compatibility with the old Rule interface
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.invalid_coordinate');
    }
}
