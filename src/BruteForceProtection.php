<?php

namespace Idez\NovaSecurity;

use Idez\NovaSecurity\Rules\InvalidUserModelRule;
use Idez\NovaSecurity\Rules\InvalidUsernameRule;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BruteForceProtection
{
    public function __construct()
    {
        $validator = Validator::make([
            // @phpstan-ignore-next-line
            'model' => NovaSecurity::getUserModel(),
            'username' => $this->getProtectedField(),
        ], [
            'model' => ['required', 'string', new InvalidUserModelRule()],
            'username' => ['required', 'string', new InvalidUsernameRule()],
        ]);


        $validator->failed();
    }

    public function getProtectedField(): string
    {
        return config('nova-security.brute_force.protected_field', 'email');
    }

    public function getUserByProtectedField(Request $request): ?Authenticatable
    {
        $field = $this->getProtectedField();

        // @phpstan-ignore-next-line
        $model = NovaSecurity::getUserModel();
        $user = $model::where($field, $request->input($field))->first();

        return $user ?? null;
    }

    public function isBruteForceProtectionEnabled(): bool
    {
        return config('nova-security.brute_force.enabled', true);
    }

}

