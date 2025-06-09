<?php

return [
    'host' => env('FTP_HOST', '93.127.190.164'),
    'username' => env('FTP_USERNAME', 'your_username'),
    'password' => env('FTP_PASSWORD', 'your_password'),
    'port' => (int) env('FTP_PORT', 21),
    'root' => env('FTP_ROOT', '/'),
    'passive' => env('FTP_PASSIVE', true),
    'ssl' => env('FTP_SSL', false),
    'timeout' => env('FTP_TIMEOUT', 120),
];
