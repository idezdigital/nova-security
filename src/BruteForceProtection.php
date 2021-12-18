<?php

namespace Idez\NovaSecurity;

use Idez\NovaSecurity\Rules\InvalidUserModelRule;
use Idez\NovaSecurity\Rules\InvalidUsernameRule;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Idez\NovaSecurity\Facades\NovaSecurity;

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

    public function getUserByProtectedField($protectedField): ?Authenticatable
    {
        $field = $this->getProtectedField();

        // @phpstan-ignore-next-line
        $model = NovaSecurity::getUserModel();
        $user = $model::where($field, $protectedField)->first();

        return $user ?? null;
    }

    public function isBruteForceProtectionEnabled(): bool
    {
        return config('nova-security.brute_force.enabled', true);
    }

    public function getBruteForceProtectionAttempts(): int
    {
        return config('nova-security.brute_force.attempts', 3);
    }

    public function attemp(Authenticatable $user, bool $match): bool
    {
        $field = $this->getProtectedField();
        $key = "nova-brute-force::{$user->getAttribute($field)}";
        $attempts = Cache::get($key, 0);

        if ($match) {
            Cache::forget($key);
            return true;
        }

        Cache::put($key, ++$attempts, config('nova-security.brute_force.ttl'));

        if ($attempts >= config('nova-security.brute_force.max_attempts')) {
            Cache::forget($key);

            $user->setAttribute('blocked_at', now())->save();

            throw ValidationException::withMessages([
                $field => [__('nova-security::validation.brute_force.max_login_attempts')],
            ]);
        }


        return false;
    }

}
