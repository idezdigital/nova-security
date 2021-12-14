<?php

namespace Idez\NovaSecurity\Http\Middleware;

use function app;
use Closure;
use function config;
use function filled;
use Idez\NovaSecurity\NovaSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use function now;
use function trans;

final class BruteForceProtection
{
    private NovaSecurity $novaSecurity;

    public function __construct()
    {
        $this->novaSecurity = app('nova-security');
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $this->novaSecurity->isBruteForceProtectionEnabled() || ! $this->isNovaLoginRoute($request)) {
            return $next($request);
        }


        $user = $this->novaSecurity->getUserByProtectedField($request);
        if (! $user) {
            return $next($request);
        }

        $field = $this->novaSecurity->getProtectedField();

        if (filled($user->getAttribute('blocked_at'))) {
            throw ValidationException::withMessages([
                'email' => [__('nova-security::validation.blocked')],
            ]);
        }


        $key = "nova-brute-force::{$user->getAttribute($field)}";
        $attempts = Cache::get($key, 0);
        $match = Hash::check($request->input($field), $user->getAttribute('password'));
        if ($match) {
            Cache::forget($key);

            return $next($request);
        }

        Cache::put($key, ++$attempts, config('nova-security.brute_force.ttl'));

        if ($attempts >= config('nova-security.brute_force.max_attempts')) {
            Cache::forget($key);

            $user->setAttribute('blocked_at', now())->save();

            throw ValidationException::withMessages([
                $field => [__('nova-security::validation.brute_force.max_login_attempts')],
            ]);
        }

        return $next($request);
    }

    private function isNovaLoginRoute(Request $request): bool
    {
        return $request->routeIs('nova.login') || $request->route()?->getName() === 'nova.login';
    }
}
