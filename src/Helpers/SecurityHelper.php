<?php

namespace Idez\NovaSecurity\Helpers;

use Idez\NovaSecurity\Exceptions\OneTimePasswordException;
use Idez\NovaSecurity\OneTimePassword;
use PragmaRX\Google2FALaravel\Support\Authenticator;

if (! function_exists('Idez\NovaSecurity\Helpers\getClientIp')) {
    /**
     * Get client ip.
     */
    function getClientIp(): ?string
    {
        return request()?->header('x-vapor-source-ip') ?? request()?->getClientIp() ?? null;
    }
}


if (! function_exists('Idez\NovaSecurity\Helpers\checkOneTimePassword')) {
    /**
     * Helper to check OTP attribute in Request
     *
     * @return bool
     * @throws OneTimePasswordException
     */
    function checkOneTimePassword(): bool
    {
        return app(OneTimePassword::class)->checkOneTimePasswordInRequest();
    }
}


if (! function_exists('Idez\NovaSecurity\Helpers\hasOTP')) {
    /**
     *
     * @param $code
     * @return bool
     */

    //@phpstan-ignore-next-line
    function hasOTP($code): bool
    {
        $two_factor_field = config('idez.nova_security.2fa.otp_secret_column');

        //@phpstan-ignore-next-line
        return app(Authenticator::class)?->verifyGoogle2FA(auth()->user()?->{$two_factor_field}, $code);
    }
}
