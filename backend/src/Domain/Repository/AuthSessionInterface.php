<?php

declare(strict_types=1);

namespace App\Domain\Repository;

/** Porta para o mecanismo de sessão (implementado em Infrastructure com $_SESSION). */
interface AuthSessionInterface
{
    public function start(int $userId): void;

    public function destroy(): void;

    public function currentUserId(): ?int;
}
