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

    'tinkoff_default' => [
        'terminal' => env('TINKOFF_TERMINAL_KEY', '1701356968335'),
        'secret' => env('TINKOFF_TERMINAL_SECRET_KEY', '1roysewgr16og6x3')
    ],

    'yandex_pay' => [
        'is_test_mode' => env('YANDEX_PAY_TEST_MODE', '0'),
        'base_test_url' => env('YANDEX_PAY_TEST_MODE_URL', 'https://sandbox.pay.yandex.ru'),
        'merchant_id' => env('YANDEX_PAY_MERCHANT_ID'),
        'api_key' => env('YANDEX_PAY_KEY'),
        'base_url' => env('YANDEX_PAY_URL'),
        'test_mode' => env('YANDEX_PAY_TEST_MODE', true)
    ],

    'dolyami' => [
//        'cert' => storage_path('app/certificate-2023-12-21-bnpl.pem'),
//        'private_key' => storage_path('app/private.key'),
        'cert' => storage_path('app/7-certificate-2024-11-28-bnpl.pem'),
        'private_key' => storage_path('app/7-private.key'),
        'login' => env('DOLYAMI_LOGIN', 'BrulerRu'),
        'password' => env('DOLYAMI_PASSWORD', 'QrvXUDpRbf')
    ],

    'vkontakte' => [
        'client_id' => env('VK_CLIENT_ID'),
        'client_secret' => env('VK_CLIENT_SECRET'),
        'redirect' => env('VK_REDIRECT_URI')
    ],

    'telegram' => [
        'bot' => env('TELEGRAM_BOT_NAME'),
        'client_id' => null,
        'client_secret' => env('TELEGRAM_TOKEN'),
        'redirect' => env('TELEGRAM_REDIRECT_URI'),
    ],


    'yandex' => [
        'client_id' => env('YANDEX_CLIENT_ID'),
        'client_secret' => env('YANDEX_CLIENT_SECRET'),
        'redirect' => env('YANDEX_REDIRECT_URI')
    ],

    'cdek' => [
        'account' => 'OFp3mQ0TCCF2fu6TfAp5SRZWxTeO7G2v',
        'password' => 'M6sX3YNFZtERVSzuyRp2zyupYJ7KdTNC',
        'api_url' => env('CDEK_API_URL'),
        'api_test_url' => env('CDEK_API_TEST_URL'),
        'api_test_mode' => env('CDEK_API_TEST_MODE'),
        'api_key' => env('CDEK_API_KEY'),
        'api_secret' => env('CDEK_API_SECRET'),

        'from' => [
            'country_code' => 'RU',
            'code' => 44,
            'city' => 'Москва',
            'address' => 'ул. Ленина, д. 1',
        ],
        'yandex_api_key' => '3837f34a-6c44-4661-be22-05f2dfcc9ac3'
    ],

    'boxberry' => [
        'url' => 'https://api.boxberry.ru/json.php',
        'api' => 'e6036cfa8cdd3b0a48390b94e870f4c4',
        'widget' => '1$LRv17l2irN6U4MZsTpDg1CkEImRncyR-'
    ],

    'dostavista' => [
        'api_token' => env('DOSTAVISTA_API_TOKEN'),
        'api_test_token' => env('DOSTAVISTA_API_TEST_TOKEN'),
        'address_sklad' => env('DOSTAVISTA_ADDRESS_SKLAD'),
        'phone_sklad' => env('DOSTAVISTA_PHONE_SKLAD'),
        'base_url' => env('DOSTAVISTA_API_URL'),
        'test_url' => env('DOSTAVISTA_API_TEST_URL'),
        'is_test_mode' => env('DOSTAVISTA_API_TEST_MODE')
    ],

];
