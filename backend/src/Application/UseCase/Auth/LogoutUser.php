<?php

declare(strict_types=1);

namespace App\Application\UseCase\Auth;

use App\Domain\Repository\AuthSessionInterface;

/** AC-005: encerra a sessão do administrador. */
final class LogoutUser
{
    public function __construct(private readonly AuthSessionInterface $session)
    {
    }

    public function execute(): void
    {
        $this->session->destroy();
    }
}
