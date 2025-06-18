<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FractionMax implements Rule
{
    private int $max;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $max)
    {
        $this->max = $max;
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
        if (! is_numeric($value['numerator']) || ! is_numeric($value['denominator'])) {
            return false;
        }

        return $value['numerator'] / $value['denominator'] <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.fraction_max', ['max' => $this->max]);
    }
}
