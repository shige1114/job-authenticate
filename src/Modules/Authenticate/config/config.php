<?php

return [
    'name' => 'Authenticate',

    'guard' => 'web',

    'username_field' => 'email',

    'password_field' => 'password',

    'remember_me_cookie' => 'remember_web',

    'auth_table' => 'users',

    'auth_model' => App\Models\User::class,

    'email_verification' => [
        'enabled' => env('AUTHENTICATE_EMAIL_VERIFICATION_ENABLED', false),
        'expire_minutes' => env('AUTHENTICATE_EMAIL_VERIFICATION_EXPIRE_MINUTES', 60),
    ],

    'password_reset' => [
        'enabled' => env('AUTHENTICATE_PASSWORD_RESET_ENABLED', true),
        'expire_minutes' => env('AUTHENTICATE_PASSWORD_RESET_EXPIRE_MINUTES', 60),
        'throttle_minutes' => env('AUTHENTICATE_PASSWORD_RESET_THROTTLE_MINUTES', 2),
    ],

    'throttle' => [
        'login_attempts' => env('AUTHENTICATE_THROTTLE_LOGIN_ATTEMPTS', 5),
        'login_decay_minutes' => env('AUTHENTICATE_THROTTLE_LOGIN_DECAY_MINUTES', 1),
    ],

    'routes' => [
        'prefix' => 'authenticate',
        'middleware' => ['web'],
        'email_verification_middleware' => ['web', 'auth'],
    ],

    'views' => [
        'login' => 'authenticate::auth.login',
        'register' => 'authenticate::auth.register',
        'verify' => 'authenticate::auth.verify',
        'forgot_password' => 'authenticate::auth.forgot-password',
        'reset_password' => 'authenticate::auth.reset-password',
    ],
];