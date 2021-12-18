<?php

namespace Idez\NovaSecurity\Http\Middleware;

use function app;
use Closure;
use Idez\NovaSecurity\NovaAuthenticator;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Exceptions\InvalidSecretKey;

class NovaTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->wantsJson()) {
            return $next($request);
        }

        try {
            $authenticator = app(NovaAuthenticator::class)->boot($request);

            if (method_exists($authenticator, 'isAuthenticated') && $authenticator->isAuthenticated()) {
                return $next($request);
            }

            if (method_exists($authenticator, 'makeRequestOneTimePasswordResponse')) {
                $authenticator->makeRequestOneTimePasswordResponse();

                return redirect()
                    ->signedRoute('nova-security.two-factor');
            }
        } catch (InvalidSecretKey $exception) {
            report($exception);

            return redirect()
                ->signedRoute('nova-security.register.two-factor');
        }


        return $next($request);
    }
}
