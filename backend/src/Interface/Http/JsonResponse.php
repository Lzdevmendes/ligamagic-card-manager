<?php

declare(strict_types=1);

namespace App\Interface\Http;

final class JsonResponse
{
    /** @param mixed $data */
    public static function send(int $status, $data): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function error(int $status, string $message, array $fields = []): void
    {
        $error = ['message' => $message];
        if ($fields !== []) {
            $error['fields'] = $fields;
        }

        self::send($status, ['error' => $error]);
    }
}
