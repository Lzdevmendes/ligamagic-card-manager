<?php

declare(strict_types=1);

require __DIR__ . '/../src/Autoload/autoloader.php';

error_reporting(E_ALL);
ini_set('display_errors', '0');

date_default_timezone_set('America/Sao_Paulo');

return [
    'database' => require __DIR__ . '/database.php',
    'frontend_origin' => getenv('FRONTEND_ORIGIN') ?: 'http://localhost:8080',
];
