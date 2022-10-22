<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ThetaFunction implements Rule
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
        // phpspreadsheetの番地指定がある場合、意図しない計算を行うのでfalseにする
        if (preg_match('/[a-zA-Z]+[0-9]+/', $value)) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.theta_function');
    }
}
