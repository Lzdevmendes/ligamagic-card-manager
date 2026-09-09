<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ValueObject\Edition;

interface EditionRepositoryInterface
{
    /** @return Edition[] */
    public function findByCardGame(string $cardGameId): array;

    public function cardGameExists(string $cardGameId): bool;
}
