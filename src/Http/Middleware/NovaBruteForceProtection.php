<?php

namespace Idez\NovaSecurity\Http\Middleware;

use function app;
use Closure;
use function filled;
use Idez\NovaSecurity\BruteForceProtection;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NovaBruteForceProtection
{
    private BruteForceProtection $bruteForceProtection;

    public function __construct()
    {
        $this->bruteForceProtection = app(BruteForceProtection::class);
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
        if (auth()->check()) {
            return $next($request);
        }

        if (! $this->bruteForceProtection->isBruteForceProtectionEnabled() || ! $this->isNovaLoginRoute($request)) {
            return $next($request);
        }


        $field = $this->bruteForceProtection->getProtectedField();
        $protectedField = $request->input($field);

        $user = $this->bruteForceProtection->getUserByProtectedField($protectedField);
        if (! $user instanceof Authenticatable) {
            return $next($request);
        }


        if (filled($user->getAttribute('blocked_at'))) {
            throw ValidationException::withMessages([
                $field => [__('nova-security::validation.blocked')],
            ]);
        }

        $match = auth()->validate($request->only($field, 'password'));
        if ($this->bruteForceProtection->attemp($user, $match)) {
            return $next($request);
        }

        return $next($request);
    }

    private function isNovaLoginRoute(Request $request): bool
    {
        return $request->routeIs('nova.login') || $request->route()?->getName() === 'nova.login';
    }
}
