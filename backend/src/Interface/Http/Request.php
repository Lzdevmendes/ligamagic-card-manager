<?php

declare(strict_types=1);

namespace App\Interface\Http;

final class Request
{
    /** @param array<string, mixed> $query @param array<string, mixed> $body */
    private function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $body,
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = rtrim((string) parse_url($uri, PHP_URL_PATH), '/');
        $path = $path === '' ? '/' : $path;

        $rawBody = file_get_contents('php://input') ?: '';
        $decoded = [];
        if ($rawBody !== '') {
            $decoded = json_decode($rawBody, true);
            $decoded = is_array($decoded) ? $decoded : [];
        }

        return new self($method, $path, $_GET, $decoded);
    }
}
