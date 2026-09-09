<?php

declare(strict_types=1);

namespace App\Application\UseCase\Card;

use App\Domain\Entity\Card;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repository\CardRepositoryInterface;

final class GetCard
{
    public function __construct(private readonly CardRepositoryInterface $cards)
    {
    }

    public function execute(int $id): Card
    {
        $card = $this->cards->findById($id);

        if ($card === null) {
            throw new NotFoundException('Carta não encontrada.');
        }

        return $card;
    }
}
