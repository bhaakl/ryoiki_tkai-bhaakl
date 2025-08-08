<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Сторонние услуги
    |--------------------------------------------------------------------------
    |
    | Этот файл предназначен для хранения учетных данных для сторонних сервисов, таких как
    | таких как Mailgun, Postmark, AWS и других. Этот файл является фактическим
    | местоположение для этого типа информации, позволяя пакетам иметь
    | обычный файл для размещения учетных данных различных служб.
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

];
