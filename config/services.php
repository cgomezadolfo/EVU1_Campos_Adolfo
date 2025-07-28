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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | UF (Unidad de Fomento) Services
    |--------------------------------------------------------------------------
    |
    | Configuración para servicios externos que proporcionan datos de UF
    |
    */

    'banco_central' => [
        'user' => env('BANCO_CENTRAL_USER'),
        'password' => env('BANCO_CENTRAL_PASSWORD'),
        'url' => env('BANCO_CENTRAL_URL', 'https://si3.bcentral.cl/SieteRestWS/SieteRestWS.ashx'),
    ],

    'sbif' => [
        'key' => env('SBIF_API_KEY'),
        'url' => env('SBIF_API_URL', 'https://api.sbif.cl/api-sbifv3/recursos_api/uf'),
    ],

    'minhacienda' => [
        'url' => env('MINHACIENDA_API_URL', 'https://mindicador.cl/api/uf'),
    ],

];
