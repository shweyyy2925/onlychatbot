<?php

return [
    'paths' => ['api/*', 'chat', 'embed/*'], 
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://127.0.0.1:8000',
        'http://localhost:8000',
        'https://troikatech.ai',
        'https://laravel-chatbot-l1zw.onrender.com',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
