<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IterationRule implements Rule
{
    private $_player;
    private $_calculation_limit;
    /**
     * Create a new rule instance.
     * @param int $player
     * @param int $calculation_limit
     * @return void
     */
    public function __construct(mixed $player, int $calculation_limit)
    {
        $this->_player = $player;
        $this->_calculation_limit = $calculation_limit;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /*
         * 演算回数は参加者数と繰り返し回数の積で計算される。
         * その積の値が $calculation_limit を超えていないか確認する。
         */
        // 参加者数がIntでない場合、他のバリデーションに引っかかるのでここはtrueを返す
        if ((int) $this->_player <= 0) {
            return true;
        }
        return $value * $this->_player <= $this->_calculation_limit;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.too_many_iterations');
    }
}
