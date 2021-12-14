<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brute Force Protection
    |--------------------------------------------------------------------------
    | This is the configuration for the Brute Force Protection.
    |--------------------------------------------------------------------------
    */
    'brute_force' => [
        /**
         * Enable Brute Force Protection.
         */
        'enabled' => true,

        /**
         * The maximum number of attempts to allow before
         * locking the user out for a given period of time.
         */
        'max_attempts' => 3,

        /**
         * The number of seconds to lock the user out for.
         */
        'ttl' => 3600,

        /**
         * The field that is used to identify the user.
         */
        'protected_field' => 'email',
    ],

    /*
    |--------------------------------------------------------------------------
    | One time password Protection
    |--------------------------------------------------------------------------
    | This is the configuration for the second factor authentication based on
    | one time passwords.
    |
    | PS: These settings. are an override of the original configuration
    | of the pragmarx/google2fa-laravel package.
    |--------------------------------------------------------------------------
    */
    'google2fa' => [
        /**
         * Uses original config file for the 2fa.
         */
        'ignore_override' => false,

        /**
         * Enable one time password.
         */
        'enabled' => true,

        /*
         * Lifetime in minutes.
         *
         * In case you need your users to be asked for a new one time passwords from time to time.
         */

        'lifetime' => env('OTP_LIFETIME', 0), // 0 = eternal,

        /*
        * Renew lifetime at every new request.
        */
        'keep_alive' => env('OTP_KEEP_ALIVE', true),

        /*
        * Auth container binding.
        */
        'auth' => 'auth',

        /*
         * Guard.
         */
        'guard' => env('NOVA_GUARD', 'web'),

        /*
         * 2FA verified session var.
         */
        'session_var' => 'google2fa',

        /*
         * One Time Password request input name.
         */
        'otp_input' => 'one_time_password',

        /*
         * One Time Password Window.
         */
        'window' => 2,

        /*
         * Forbid user to reuse One Time Passwords.
         */
        'forbid_old_passwords' => false,

        /*
         * User's table column for google2fa secret.
         */
        'otp_secret_column' => 'google2fa_secret',

        /*
         * One Time Password View.
         */
        'view' => 'nova::auth.two_factor',

        /*
         * One Time Password error message.
         */
        'error_messages' => [
            'wrong_otp'       => "The 'One Time Password' typed was wrong.",
            'cannot_be_empty' => 'One Time Password cannot be empty.',
            'unknown'         => 'An unknown error has occurred. Please try again.',
        ],

        /*
         * Throw exceptions or just fire events?
         */
        'throw_exceptions' => env('OTP_THROW_EXCEPTION', true),

        /*
         * Which image backend to use for generating QR codes?
         *
         * Supports imagemagick, svg and eps
         */
        'qrcode_image_backend' => \PragmaRX\Google2FALaravel\Support\Constants::QRCODE_IMAGE_BACKEND_SVG,

        /**
         * Maximum limit of invalid attempts to disable 2FA on the device.
         */
        'invalid_attempts_limit' => 10,
    ]

];
