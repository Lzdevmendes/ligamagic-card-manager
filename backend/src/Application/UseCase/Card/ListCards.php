<?php

declare(strict_types=1);

namespace App\Application\UseCase\Card;

use App\Domain\Repository\CardRepositoryInterface;

/** AC-006: lista todas as cartas cadastradas. */
final class ListCards
{
    public function __construct(private readonly CardRepositoryInterface $cards)
    {
    }

    /** @return \App\Domain\Entity\Card[] */
    public function execute(): array
    {
        return $this->cards->findAll();
    }
}
