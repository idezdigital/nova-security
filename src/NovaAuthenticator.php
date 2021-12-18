<?php

namespace Idez\NovaSecurity;

use Idez\NovaSecurity\Exceptions\OneTimePasswordException;
use PragmaRX\Google2FALaravel\Exceptions\InvalidOneTimePassword;
use PragmaRX\Google2FALaravel\Exceptions\InvalidSecretKey;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class NovaAuthenticator extends Authenticator
{
    /**
     * @throws OneTimePasswordException|InvalidSecretKey
     */
    public function checkOneTimePasswordInRequest(): bool
    {
        if ($this->isActivated()) {
            return true;
        }

        $otpInputName = $this->getInputName();
        $otpValue = $this->getInputOneTimePassword();

        if (! $otpValue) {
            $errorKey = 'missing_attribute';

            throw new OneTimePasswordException(trans("nova-security::2fa.$errorKey", ['name' => $otpInputName]), 400, key: $errorKey);
        }

        if (! $this->isValidOtpPattern($otpValue)) {
            $errorKey = 'invalid_pattern';

            throw new OneTimePasswordException(trans("nova-security::2fa.$errorKey", ['name' => $otpInputName]), 400, key: $errorKey);
        }

        if (! $this->checkOTP()) {
            $errorKey = 'invalid_otp';

            throw new OneTimePasswordException(trans("nova-security::2fa.$errorKey"), 403, key: $errorKey);
        }


        return true;
    }

    /**
     * Check if the 2FA is activated for the user.
     *
     * @return bool
     * @throws InvalidSecretKey
     */
    public function isActivated(): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $secret = $this->getGoogle2FASecretKey();

        if (blank($secret) && config('nova-security.google2fa.require_for_all', false)) {
            throw new InvalidSecretKey('Secret key is not set.');
        }

        return ! empty($secret);
    }

    /**
     * Get Input Name for OTP in Requests
     *
     * @return string
     */
    public function getInputName(): string
    {
        return config('google2fa.otp_input');
    }

    /**
     * Verify if is Valid OTP Pattern
     *
     * @param string $otpToken
     * @return bool
     */
    private function isValidOtpPattern(string $otpToken): bool
    {
        $otpPattern = '/^\d{6}$/';

        return (bool)preg_match($otpPattern, $otpToken);
    }

    /**
     * Verify the OTP.
     *
     * @return bool
     * @throws InvalidOneTimePassword
     *
     */
    public function verifyOneTimePassword(): bool
    {
        return parent::verifyOneTimePassword();
    }
}
