<?php

// return [

//     /*
//     |--------------------------------------------------------------------------
//     | Cross-Origin Resource Sharing (CORS) Configuration
//     |--------------------------------------------------------------------------
//     |
//     | Here you may configure your settings for cross-origin resource sharing
//     | or "CORS". This determines what cross-origin operations may execute
//     | in web browsers. You are free to adjust these settings as needed.
//     |
//     | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
//     |
//     */

//     'paths' => ['api/*', 'sanctum/csrf-cookie'],

//     'allowed_methods' => ['*'],

//     'allowed_origins' => ['*'],

//     'allowed_origins_patterns' => [],

//     'allowed_headers' => ['*'],

//     'exposed_headers' => [],

//     'max_age' => 0,

//     'supports_credentials' => true,

// ];

return [
    // 'paths' => ['api/*', 'sanctum/csrf-cookie'],
    // 'allowed_methods' => ['*'],
    // 'allowed_origins' => ['https://mixsyria.com','http://localhost:3000', 'http://localhost:8080','http://localhost:8000', 'http://haraj-syria.test'],
    // 'allowed_headers' => ['*'],
    // 'exposed_headers' => [],
    // 'max_age' => 0,
    // 'supports_credentials' => true,



'paths' => ['api/*', 'sanctum/csrf-cookie'],

'allowed_methods' => ['*'],

'allowed_origins' => ['*'],

'allowed_origins_patterns' => [],

'allowed_headers' => ['*'],

'exposed_headers' => [],

'max_age' => 0,

'supports_credentials' => false,
];

