<?php

namespace Idez\NovaSecurity\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use PragmaRX\Google2FALaravel\Google2FA;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use function app;

class TwoFactor
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
        /**
         * @var Google2FA|null $authenticator
         */
        $authenticator = app(Authenticator::class)->boot($request);

        if (method_exists($authenticator, 'isAuthenticated') && $authenticator->isAuthenticated()) {
            return $next($request);
        }

        if(method_exists($authenticator, 'makeRequestOneTimePasswordResponse')) {
            return $authenticator->makeRequestOneTimePasswordResponse();
        }


        return $next($request);
    }

}
