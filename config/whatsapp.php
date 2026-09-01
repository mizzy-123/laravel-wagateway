<?php

return [

    'base_url' => rtrim(env('WA_BASE_URL', 'http://localhost:21465'), '/'),

    'secret_key' => env('WA_SECRET_KEY', ''),

    'webhook_secret' => env('WA_WEBHOOK_SECRET'),

    'webhook_url' => env('WA_WEBHOOK_URL') ?: rtrim((string) env('APP_URL', 'http://localhost'), '/').'/api/webhook/whatsapp',

    'http' => [
        'connect_timeout' => (int) env('WA_HTTP_CONNECT_TIMEOUT', 5),
        'timeout' => (int) env('WA_HTTP_TIMEOUT', 30),
    ],

];
