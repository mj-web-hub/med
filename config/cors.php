<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
        'mobile/*',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:19000', // Expo Dev Tools
        'http://localhost:19006', // Expo Web App
        // Add more development environments as needed
    ],

    'allowed_headers' => ['*'],

    'supports_credentials' => true,
];