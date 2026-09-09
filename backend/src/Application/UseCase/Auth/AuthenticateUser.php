<?php

declare(strict_types=1);

namespace App\Application\UseCase\Auth;

use App\Domain\Entity\User;
use App\Domain\Exception\UnauthorizedException;
use App\Domain\Repository\AuthSessionInterface;
use App\Domain\Repository\UserRepositoryInterface;

/** AC-001, AC-002: autentica e-mail/senha e abre uma sessão. */
final class AuthenticateUser
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly AuthSessionInterface $session,
    ) {
    }

    public function execute(string $email, string $password): User
    {
        $user = $this->users->findByEmail(trim($email));

        if ($user === null || !$user->verifyPassword($password)) {
            throw new UnauthorizedException('E-mail ou senha inválidos.');
        }

        $this->session->start($user->id());

        return $user;
    }
}
