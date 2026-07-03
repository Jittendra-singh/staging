<?php

return [
    'default_locale' => env('TRANSLATIONS_DEFAULT_LOCALE', config('app.locale', 'en')),

    'supported_locales' => ['en', 'es', 'ht'],

    /*
    |--------------------------------------------------------------------------
    | Automatic Translation
    |--------------------------------------------------------------------------
    |
    | Keep this disabled unless a real provider and queue worker are configured.
    | Manual JSON translations and cached translations are still read normally.
    |
    */
    'auto_translate' => env('TRANSLATIONS_AUTO_TRANSLATE', false),
    'provider' => env('TRANSLATIONS_PROVIDER', 'none'),
    'queue_name' => env('TRANSLATIONS_QUEUE', 'default'),
    'cache_ttl' => env('TRANSLATIONS_CACHE_TTL'),
    'database_store' => env('TRANSLATIONS_DATABASE_STORE', true),
    'circuit_breaker_minutes' => env('TRANSLATIONS_CIRCUIT_BREAKER_MINUTES', 15),

    'http' => [
        'timeout' => env('TRANSLATIONS_HTTP_TIMEOUT', 10),
        'verify' => env('TRANSLATIONS_HTTP_VERIFY', true),
    ],

    'providers' => [
        'google_cloud' => [
            'endpoint' => env('GOOGLE_TRANSLATE_ENDPOINT', 'https://translation.googleapis.com/language/translate/v2'),
            'key' => env('GOOGLE_TRANSLATE_API_KEY'),
        ],

        'libretranslate' => [
            'endpoint' => env('LIBRETRANSLATE_ENDPOINT'),
            'key' => env('LIBRETRANSLATE_API_KEY'),
        ],
    ],
];
