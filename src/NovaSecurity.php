<?php

namespace Idez\NovaSecurity;

use Idez\NovaSecurity\Rules\InvalidUserModelRule;
use Idez\NovaSecurity\Rules\InvalidUsernameRule;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NovaSecurity
{
    public function __construct()
    {
        $validator = Validator::make([
            'model' => $this->getUserModel(),
            'username' => $this->getProtectedField(),
        ], [
            'model' => ['required', 'string', new InvalidUserModelRule()],
            'username' => ['required', 'string', new InvalidUsernameRule()],
        ]);


        $validator->failed();
    }

    public function getGuard(): \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
    {
        $name = $this->getGuardName();

        return auth()->guard($name);
    }

    protected function getGuardName(): string
    {
        return config('nova.guard', 'web') ?? config('auth.defaults.guard');
    }

    public function getUserModel(): string
    {
        $guard = $this->getGuardName();

        $provider = config("auth.guards.{$guard}.provider");

        return config("auth.providers.{$provider}.model");
    }

    public function getProtectedField(): string
    {
        return config('nova-security.brute_force.protected_field', 'email');
    }

    public function getUserByProtectedField(Request $request): ?Authenticatable
    {
        $field = $this->getProtectedField();
        $model = $this->getUserModel();

        $user = $model::where($field,$request->input($field))->first();

        return $user ?? null;
    }

    public function isBruteForceProtectionEnabled(): bool
    {
        return config('nova-security.brute_force.enabled', true);
    }
}
