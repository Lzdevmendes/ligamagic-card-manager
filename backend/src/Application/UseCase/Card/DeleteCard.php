<?php

declare(strict_types=1);

namespace App\Application\UseCase\Card;

use App\Domain\Exception\NotFoundException;
use App\Domain\Repository\CardRepositoryInterface;

/** AC-011: exclui uma carta existente. */
final class DeleteCard
{
    public function __construct(private readonly CardRepositoryInterface $cards)
    {
    }

    public function execute(int $id): void
    {
        if ($this->cards->findById($id) === null) {
            throw new NotFoundException('Carta não encontrada.');
        }

        $this->cards->delete($id);
    }
}
