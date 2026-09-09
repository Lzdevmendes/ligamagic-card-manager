<?php

declare(strict_types=1);

namespace App\Application\UseCase\Auth;

use App\Domain\Entity\User;
use App\Domain\Exception\UnauthorizedException;
use App\Domain\Repository\AuthSessionInterface;
use App\Domain\Repository\UserRepositoryInterface;

/** AC-003, AC-004: resolve o usuário autenticado a partir da sessão atual. */
final class GetCurrentUser
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly AuthSessionInterface $session,
    ) {
    }

    public function execute(): User
    {
        $userId = $this->session->currentUserId();

        if ($userId === null) {
            throw new UnauthorizedException('Não autenticado.');
        }

        $user = $this->users->findById($userId);

        if ($user === null) {
            throw new UnauthorizedException('Não autenticado.');
        }

        return $user;
    }
}
