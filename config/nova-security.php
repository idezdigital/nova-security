<?php
// config for Idez/NovaSecurity
return [


    /*
    |--------------------------------------------------------------------------
    | Brute Force Protection
    |--------------------------------------------------------------------------
    |
    | This is the configuration for the Brute Force Protection.
    |
    | - Active: Set to true to enable the Brute Force Protection.
    |
    | - Max Attempts: The maximum number of attempts to allow before
    |   locking the user out for a given period of time.
    |
    | - TTL: The number of seconds to lock the user out for.
    |
    | - Protected Field: The field that is used to identify the user.
    |
    */
    'brute_force' => [
        'enabled' => true,
        'max_attempts' => 3,
        'ttl' => 3600,
        'protected_field' => 'email',
    ],


    '2fa' => [
        'enabled' => true,
        'invalid_attempts_limit'
    ]

];
