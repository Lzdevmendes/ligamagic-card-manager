<?php

declare(strict_types=1);

// Configuração de conexão com o banco, lida de variáveis de ambiente
// (definidas no docker-compose.yml / .env). Nunca hard-coded.
return [
    'host' => getenv('DB_HOST') ?: 'db',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_DATABASE') ?: 'ligamagic',
    'user' => getenv('DB_USER') ?: 'ligamagic',
    'password' => getenv('DB_PASSWORD') ?: '',
];
