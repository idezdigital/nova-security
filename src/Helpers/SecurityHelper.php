<?php

namespace Idez\NovaSecurity\Helpers;

use Idez\NovaSecurity\Exceptions\OneTimePasswordException;
use Idez\NovaSecurity\NovaAuthenticator;


if (!function_exists('Idez\NovaSecurity\Helpers\checkOtp')) {
    /**
     * Helper to check OTP attribute in Request
     *
     * @return bool
     * @throws OneTimePasswordException
     */
    function checkOtp(): bool
    {
        // @phpstan-ignore-next-line
        return app(NovaAuthenticator::class)?->boot(request())?->checkOneTimePasswordInRequest() ?? false;
    }
}


if (!function_exists('Idez\NovaSecurity\Helpers\verifyOTP')) {
    /**
     * Helper to check if OTP is enabled
     * @param $code
     * @return bool
     */

    //@phpstan-ignore-next-line
    function verifyOTP($code): bool
    {
        $authenticator = app(NovaAuthenticator::class)->boot(request());

        // @phpstan-ignore-next-line
        $secret = $authenticator?->getInputOneTimePassword();
        return $authenticator->verifyGoogle2FA($secret, $code);
    }
}
