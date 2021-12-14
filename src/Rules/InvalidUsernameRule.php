<?php

namespace Idez\NovaSecurity\Rules;

use Illuminate\Contracts\Validation\Rule;

class InvalidUsernameRule implements Rule
{
    private string $value;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $model = config('nova-security.user.model');
        $this->value = $value;

        return property_exists($model, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('nova-security::validation.exceptions.invalid_username', ['attribute' => $this->value]);
    }
}
