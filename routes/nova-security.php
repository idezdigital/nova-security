<?php

use Idez\NovaSecurity\Http\Controllers\OneTimePasswordController;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. You're free to add
| as many additional routes to this file as your tool may require.
|
*/

Route::namespace('Idez\NovaSecurity\Http\Controllers')
    ->domain(config('nova.domain'))
    ->prefix(\Laravel\Nova\Nova::path())
    ->middleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ])
    ->group(function () {
        Route::get('/two-factor', [OneTimePasswordController::class, 'show'])
            ->name('nova-security.two-factor')
            ->middleware(ValidateSignature::class);

        Route::post('/two-factor', [OneTimePasswordController::class, 'verify'])
            ->name('nova-security.two-factor-verify');
    });
