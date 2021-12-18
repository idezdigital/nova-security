<?php

namespace Idez\NovaSecurity\Http\Controllers;

use Idez\NovaSecurity\NovaAuthenticator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OneTimePasswordController
{

    public function __construct(private NovaAuthenticator $authenticator)
    {
    }

    /**
     * Show the application dashboard.
     * @param Request $request
     * @return RedirectResponse|Response|JsonResponse
     */
    public function show(Request $request): RedirectResponse|Response|JsonResponse
    {
        $authenticator = $this->authenticator->boot($request);

        if (method_exists($authenticator, 'makeRequestOneTimePasswordResponse')) {
            return $authenticator->makeRequestOneTimePasswordResponse();
        }

        return redirect()->route('nova.login');
    }

    public function verify(Request $request): RedirectResponse|Response|JsonResponse
    {
        $authenticator = $this->authenticator->boot($request);

        // @phpstan-ignore-next-line
        if ($authenticator->verifyOneTimePassword())
        {
            $authenticator->login();
        }

        return redirect()->route('nova.login');
    }
}
