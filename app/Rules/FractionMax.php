<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FractionMax implements ValidationRule
{
    private int $max;

    // For backward compatibility
    private $validationFailed = false;

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
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value['numerator']) || ! is_numeric($value['denominator'])) {
            $this->validationFailed = true;
            $fail(trans('validation.fraction_max', ['max' => $this->max]));

            return;
        }

        if ($value['numerator'] / $value['denominator'] > $this->max) {
            $this->validationFailed = true;
            $fail(trans('validation.fraction_max', ['max' => $this->max]));
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
    public function passes($attribute, $value)
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
        return trans('validation.fraction_max', ['max' => $this->max]);
    }
}
