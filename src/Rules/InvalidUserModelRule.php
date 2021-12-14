<?php

namespace Idez\NovaSecurity\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Auth\User as Authenticatable;
use ReflectionClass;

class InvalidUserModelRule implements Rule
{
    private string $value;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  string  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->value = $value;

        if(! class_exists($value)) {
            return false;
        }

        $reflection = new ReflectionClass($value);
        if(! $reflection->isSubclassOf(Authenticatable::class)) {
            return false;
        }


        return true;
    }

    public function message()
    {
        return trans('nova-security::validation.exceptions.invalid_user_model', ['attribute' => $this->value]);
    }
}
