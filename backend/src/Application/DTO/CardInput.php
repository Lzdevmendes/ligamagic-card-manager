<?php

declare(strict_types=1);

namespace App\Application\DTO;

/** Dados brutos de entrada, vindos do corpo da requisição HTTP, sem validação ainda. */
final class CardInput
{
    public function __construct(
        public readonly string $nameEn,
        public readonly ?string $namePt,
        public readonly string $cardGameId,
        public readonly string $editionId,
        public readonly string $rarity,
        public readonly ?string $imageUrl,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            nameEn: (string) ($data['name_en'] ?? ''),
            namePt: isset($data['name_pt']) && $data['name_pt'] !== null ? (string) $data['name_pt'] : null,
            cardGameId: (string) ($data['card_game'] ?? ''),
            editionId: (string) ($data['edition_id'] ?? ''),
            rarity: (string) ($data['rarity'] ?? ''),
            imageUrl: isset($data['image_url']) && $data['image_url'] !== null ? (string) $data['image_url'] : null,
        );
    }
}
