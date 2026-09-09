<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Repository\AuthSessionInterface;

/** Sessão de autenticação baseada em $_SESSION nativo do PHP. */
final class PhpSessionAuthService implements AuthSessionInterface
{
    private const SESSION_KEY = 'user_id';

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public function start(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $userId;
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function currentUserId(): ?int
    {
        $id = $_SESSION[self::SESSION_KEY] ?? null;

        return is_int($id) ? $id : null;
    }
}
