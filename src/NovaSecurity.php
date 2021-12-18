<?php

namespace Idez\NovaSecurity;

class NovaSecurity
{
    protected function getGuardName(): string
    {
        return config('nova.guard') ?? config('nova-security.google2fa.guard') ?? config('auth.defaults.guard') ?? 'web';
    }

    public function getUserModel($guard = null): string
    {
        if (! $guard) {
            $guard = $this->getGuardName();
        }

        $provider = config("auth.guards.{$guard}.provider");

        return config("auth.providers.{$provider}.model");
    }
}
