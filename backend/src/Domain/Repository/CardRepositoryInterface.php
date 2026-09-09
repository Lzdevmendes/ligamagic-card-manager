<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Card;

interface CardRepositoryInterface
{
    /** @return Card[] */
    public function findAll(): array;

    public function findById(int $id): ?Card;

    /** Insere e devolve a carta com o id gerado preenchido. */
    public function insert(Card $card): Card;

    public function update(int $id, Card $card): Card;

    public function delete(int $id): void;

    public function editionExists(string $editionId, string $cardGameId): bool;
}
