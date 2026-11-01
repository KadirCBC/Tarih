<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Configuration for Numismatic AI Vision and Vector Recognition Services.
    |
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'api_url' => 'https://generativelanguage.googleapis.com/v1beta/models',
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'vector_service' => [
        'url' => env('VECTOR_SERVICE_URL', 'http://127.0.0.1:8000'),
        'timeout' => env('VECTOR_SERVICE_TIMEOUT', 10),
    ],

];

