<?php

declare(strict_types=1);

namespace App\Application\UseCase\Card;

use App\Application\DTO\CardInput;
use App\Domain\Entity\Card;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\ValidationException;
use App\Domain\Repository\CardRepositoryInterface;

/** AC-009, AC-010: valida e atualiza uma carta existente. */
final class UpdateCard
{
    public function __construct(private readonly CardRepositoryInterface $cards)
    {
    }

    public function execute(int $id, CardInput $input): Card
    {
        if ($this->cards->findById($id) === null) {
            throw new NotFoundException('Carta não encontrada.');
        }

        $card = Card::create(
            $input->nameEn,
            $input->namePt,
            $input->cardGameId,
            $input->editionId,
            $input->rarity,
            $input->imageUrl,
        );

        if (!$this->cards->editionExists($card->editionId(), $card->cardGame()->value())) {
            throw new ValidationException([
                'edition_id' => 'Edição não pertence ao Card Game selecionado.',
            ]);
        }

        return $this->cards->update($id, $card);
    }
}
