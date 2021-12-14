<?php

namespace Idez\NovaSecurity;

use Idez\NovaSecurity\Exceptions\OneTimePasswordException;
use Idez\NovaSecurity\Models\Device;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OneTimePassword
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = app('pragmarx.google2fa');
    }

    /**
     * Create 2FA Secret and Auth URL
     *
     * @param Device $device
     * @return array
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function create2FASecretAndAuthUrl(Device $device): array
    {
        $secret = $this->google2fa->generateSecretKey();
        $authUrl = $this->getOtpUrl($device, $secret);

        return compact('authUrl', 'secret');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getOtpUrl(Device $device, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            app('config')->get('app.name'),
            $device->id,
            $secret
        );
    }

    /**
     * Verify if is Valid 2FA Secret Token
     *
     * @param Device $device
     * @param string $oneTimePassword
     * @return bool
     *
     * @throws OneTimePasswordException
     */
    public function isValid2FASecretToken(Device $device, string $oneTimePassword): bool
    {
        $device2faSecret = $this->getDevice2faSecret($device);

        if (!$device2faSecret) {
            $errorKey = '2fa.device_not_have_2fa';

            throw new OneTimePasswordException(trans("errors.auth.$errorKey"), 400, $errorKey);
        }

        $isValidOtp = $this->google2fa->verifyGoogle2FA($device2faSecret, $oneTimePassword);
        $isValidOtp ?: $this->handleInvalidAttempt($device);

        return $isValidOtp;
    }

    /**
     * Check OTP Attribute in request
     *
     * @return bool
     * @throws OneTimePasswordException
     */
    public function checkOneTimePasswordInRequest(): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        $otpInputName = $this->getInputName();
        $otpValue = request()->input($otpInputName);

        if (!$otpValue) {
            $errorKey = 'auth.2fa.missing_attribute';

            throw new OneTimePasswordException(trans("errors.$errorKey", ['name' => $otpInputName]), 400, $errorKey);
        }

        if (!$this->isValidOtpPattern($otpValue)) {
            $errorKey = 'auth.2fa.invalid_pattern';

            throw new OneTimePasswordException(trans("errors.$errorKey", ['name' => $otpInputName]), 400, $errorKey);
        }

        try {
            $verifiedDevice = Device::verified()->firstOrFail();
        } catch (ModelNotFoundException) {
            $errorKey = '2fa.not_have_verified_devices';

            throw new OneTimePasswordException(trans("errors.auth.$errorKey"), 403, $errorKey);
        }

        $isValidOtp = $this->isValid2FASecretToken($verifiedDevice, $otpValue);

        if (!$isValidOtp) {
            $errorKey = '2fa.invalid_otp';

            throw new OneTimePasswordException(trans("errors.auth.$errorKey"), 403, $errorKey);
        }

        return true;
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
     * Check if 2FA is Enable in Tenant
     *
     * @return bool
     */
    private function isEnabled(): bool
    {
        return config('nova-security.2fa.active');
    }

    /**
     * Handle Invalid OTP Attempt
     *
     * @param Device $device
     * @return void
     * @throws OneTimePasswordException
     */
    private function handleInvalidAttempt(Device $device): void
    {
        $limitInvalidAttempts = config('nova-security.invalid_attempts_limit');
        $device->increment('2fa_invalid_attempts');

        if ($device->{'2fa_invalid_attempts'} >= $limitInvalidAttempts) {
            $device->disableDevice2fa();
            $errorKey = '2fa.device_disabled_by_invalid_attempts';

            throw new OneTimePasswordException(trans("errors.auth.$errorKey"), 403, $errorKey);
        }
    }

    /**
     * Get Current Valid OTP Based in Device Secret
     *
     * @param Device $device
     * @return string
     *
     * @throws OneTimePasswordException
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function getCurrentOtp(Device $device): string
    {
        $device2faSecret = $this->getDevice2faSecret($device);

        if (!$device2faSecret) {
            $errorKey = '2fa.device_not_have_2fa';

            throw new OneTimePasswordException(trans("errors.auth.$errorKey"), 400, $errorKey);
        }

        return $this->google2fa->getCurrentOtp($device2faSecret);
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
     * Get Device 2FA Secret Key
     *
     * @param Device $device
     * @return string
     */
    public function getDevice2faSecret(Device $device): string
    {
        return $device->{'2fa_secret_key'};
    }
}
