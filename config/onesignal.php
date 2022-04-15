<?php

return [
    'app_id' => env('ONESIGNAL_APP_ID'),
    'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),

    'http' => [
        'timeout' => 30,
        'retry' => 2,
    ]
];
