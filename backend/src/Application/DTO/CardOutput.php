<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\Card;

/** Formata uma entidade Card para o formato de resposta JSON da API. */
final class CardOutput
{
    /** @return array<string, mixed> */
    public static function fromEntity(Card $card): array
    {
        return [
            'id' => $card->id(),
            'name_en' => $card->nameEn(),
            'name_pt' => $card->namePt(),
            'card_game' => $card->cardGame()->value(),
            'edition_id' => $card->editionId(),
            'rarity' => $card->rarity()->value(),
            'image_url' => $card->imageUrl(),
        ];
    }

    /**
     * @param Card[] $cards
     * @return array<int, array<string, mixed>>
     */
    public static function fromEntities(array $cards): array
    {
        return array_map(self::fromEntity(...), $cards);
    }
}
