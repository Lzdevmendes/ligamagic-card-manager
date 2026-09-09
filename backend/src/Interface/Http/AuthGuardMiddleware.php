<?php

declare(strict_types=1);

namespace App\Interface\Http;

use App\Application\UseCase\Auth\GetCurrentUser;
use App\Domain\Entity\User;
use App\Domain\Exception\UnauthorizedException;

/** AC-003: bloqueia endpoints protegidos quando não há sessão ativa. */
final class AuthGuardMiddleware
{
    public function __construct(private readonly GetCurrentUser $getCurrentUser)
    {
    }

    /** Retorna o usuário autenticado ou responde 401 e devolve null. */
    public function requireUser(): ?User
    {
        try {
            return $this->getCurrentUser->execute();
        } catch (UnauthorizedException $e) {
            JsonResponse::error(401, $e->getMessage());

            return null;
        }
    }
}
