<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Explicitly allow your frontend
    'allowed_origins' => ['http://localhost:8081'],

    'allowed_origins_patterns' => [],

    // Allow all headers — prevents "missing header" CORS errors
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Needed for cookies or Authorization headers
    'supports_credentials' => true,
];