<?php
// config for Idez/NovaSecurity
return [
    'brute_force' => [
        'max_attempts' => 3,
        'ttl' => 3600,
    ],
    'user_model' => \App\Models\User::class,
    'username_field' => 'email',
];
