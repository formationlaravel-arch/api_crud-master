<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'api_demo',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'jwt' => [
        'secret' => 'v2e8s3Zp9QmR4xYcL7wN1uD5FjH0kB2t',
        'issuer' => 'http://localhost',
        'audience' => 'http://localhost',
        'expire' => 3600,
    ],
];
