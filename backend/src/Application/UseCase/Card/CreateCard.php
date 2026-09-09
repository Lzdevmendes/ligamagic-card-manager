<?php

declare(strict_types=1);

namespace App\Application\UseCase\Card;

use App\Application\DTO\CardInput;
use App\Domain\Entity\Card;
use App\Domain\Exception\ValidationException;
use App\Domain\Repository\CardRepositoryInterface;

/** AC-007, AC-008: valida e cria uma nova carta. */
final class CreateCard
{
    public function __construct(private readonly CardRepositoryInterface $cards)
    {
    }

    public function execute(CardInput $input): Card
    {
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

        return $this->cards->insert($card);
    }
}
