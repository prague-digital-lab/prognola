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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // config/services.php
    'discord' => [
        'token' => env('DISCORD_API_TOKEN'),
        'channel_reservations_id' => env('DISCORD_CHANNEL_RESERVATIONS_ID'),
        'channel_vouchers_id' => env('DISCORD_CHANNEL_VOUCHERS_ID'),
    ],

    'abraflexi' => [
        'company' => env('ABRAFLEXI_COMPANY'),
        'url' => env('ABRAFLEXI_URL'),
        'login' => env('ABRAFLEXI_LOGIN'),
        'password' => env('ABRAFLEXI_PASSWORD'),
        'timeout' => env('ABRAFLEXI_TIMEOUT', 60),
        'exceptions' => env('ABRAFLEXI_EXCEPTIONS', true),
    ],

    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
    ],

];
