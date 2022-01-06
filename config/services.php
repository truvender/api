<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'termii' => [
        'key' => env('TERMII_API_KEY')
    ],
    'wallets_africa' => [
        'pk' => env('WA_PUBLIC_KEY'),
        'sk' => env('WA_SECRET_KEY'),
        'url' => env('WA_BASE_URL'),
    ],
    
    'flutterwave-test' => [
        'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
        'secrete_key' => env('FLUTTERWAVE_SECRETE_KEY'),
        'encyt_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
        'root-url' => env('FLUTTERWAVE_ROOT_URL'),
    ],

    'flutterwave' => [
        'public_key' => env('FLUTTERWAVE_TEST_PUBLIC_KEY'),
        'secrete_key' => env('FLUTTERWAVE_TEST_SECRETE_KEY'),
        'encyt_key' => env('FLUTTERWAVE_TEST_ENCRYPTION_KEY'),
        'root-url' => env('FLUTTERWAVE_ROOT_URL'),
    ],

];
