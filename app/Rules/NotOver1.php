<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Phospr\Fraction;

class NotOver1 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $fraction = new Fraction(0, 1);
        foreach ($value as $lottery_rate) {
            $add_data = new Fraction((int) $lottery_rate['rate_number'], pow(10, (int) $lottery_rate['rate_digit']));
            $fraction = $fraction->add($add_data);
        }
        return $fraction->toFloat() <= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.not_over_1');
    }
}
