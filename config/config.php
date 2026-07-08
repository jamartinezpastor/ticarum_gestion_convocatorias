<?php
return [
    'db' => [
        'host' => getenv('DB_HOST')     ?: '127.0.0.1',
        'name' => getenv('DB_DATABASE') ?: 'ticarum_convocatorias',
        'user' => getenv('DB_USERNAME') ?: 'root',
        'pass' => getenv('DB_PASSWORD') ?: '',
    ],
];