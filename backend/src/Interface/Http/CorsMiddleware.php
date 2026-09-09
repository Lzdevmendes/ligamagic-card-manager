<?php

declare(strict_types=1);

namespace App\Interface\Http;

/**
 * Frontend (porta 8080) e API (porta 8091) rodam em origens diferentes,
 * então precisamos de CORS explícito + cookies de sessão cross-port.
 */
final class CorsMiddleware
{
    public static function apply(string $allowedOrigin): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if ($origin !== '' && $origin === $allowedOrigin) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
