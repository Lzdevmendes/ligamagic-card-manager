<?php

declare(strict_types=1);

namespace App\Application\UseCase\Edition;

use App\Domain\Repository\EditionRepositoryInterface;
use App\Domain\ValueObject\CardGame;

/** AC-013, AC-014, AC-015: edições disponíveis para um Card Game. */
final class GetEditionsByGame
{
    public function __construct(private readonly EditionRepositoryInterface $editions)
    {
    }

    /** @return \App\Domain\ValueObject\Edition[] */
    public function execute(string $cardGameId): array
    {
        $cardGame = CardGame::fromString($cardGameId);

        return $this->editions->findByCardGame($cardGame->value());
    }
}
