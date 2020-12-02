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

    'inbenta' => [
        'api_key' => env('INBENTA_API_KEY'),
        'secret' => env('INBENTA_SECRET'),
        'refresh_token_endpoint' => env('INBENTA_AUTH_REFRESH_ENDPOINT'),
        'conversation_endpoint' => env('INBENTA_API_CONVERSATION_ENDPOINT'),
        'conversation_message_endpoint' => env('INBENTA_API_CONVERSATION_MESSAGE_ENDPOINT'),
        'conversation_history_endpoint' => env('INBENTA_API_CONVERSATION_HISTORY_ENDPOINT'),
    ],
    'inbenta-auth' => [
        'url' => env('INBENTA_AUTH_URL'),
        'auth_endpoint' => env('INBENTA_AUTH_ENDPOINT'),
        'refresh_token_endpoint' => env('INBENTA_AUTH_REFRESH_TOKEN_ENDPOINT')

    ],
    'inbentasw' => [
        'api_url' => env('INBENTA_SW_API_URL'),
    ],

];
