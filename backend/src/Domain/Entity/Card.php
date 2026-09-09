<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\ValidationException;
use App\Domain\ValueObject\CardGame;
use App\Domain\ValueObject\Rarity;

final class Card
{
    private function __construct(
        private readonly ?int $id,
        private readonly string $nameEn,
        private readonly ?string $namePt,
        private readonly CardGame $cardGame,
        private readonly string $editionId,
        private readonly Rarity $rarity,
        private readonly ?string $imageUrl,
    ) {
    }

    /**
     * Cria e valida uma carta nova (sem id ainda) a partir de dados brutos vindos da borda (HTTP).
     * Lança ValidationException com todos os campos inválidos de uma vez (AC-008).
     */
    public static function create(
        string $nameEn,
        ?string $namePt,
        string $cardGameId,
        string $editionId,
        string $rarity,
        ?string $imageUrl,
    ): self {
        $errors = [];

        $nameEn = trim($nameEn);
        if ($nameEn === '') {
            $errors['name_en'] = 'Nome da carta em inglês é obrigatório.';
        }

        $editionId = trim($editionId);
        if ($editionId === '') {
            $errors['edition_id'] = 'Edição é obrigatória.';
        }

        $cardGame = null;
        try {
            $cardGame = CardGame::fromString($cardGameId);
        } catch (ValidationException $e) {
            $errors = array_merge($errors, $e->fields());
        }

        $rarityVo = null;
        try {
            $rarityVo = Rarity::fromString($rarity);
        } catch (ValidationException $e) {
            $errors = array_merge($errors, $e->fields());
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $namePt = $namePt !== null ? trim($namePt) : null;
        $namePt = ($namePt === '') ? null : $namePt;

        $imageUrl = $imageUrl !== null ? trim($imageUrl) : null;
        $imageUrl = ($imageUrl === '') ? null : $imageUrl;

        /** @var CardGame $cardGame */
        /** @var Rarity $rarityVo */
        return new self(null, $nameEn, $namePt, $cardGame, $editionId, $rarityVo, $imageUrl);
    }

    public static function fromPersistence(
        int $id,
        string $nameEn,
        ?string $namePt,
        string $cardGameId,
        string $editionId,
        string $rarity,
        ?string $imageUrl,
    ): self {
        return new self(
            $id,
            $nameEn,
            $namePt,
            CardGame::fromString($cardGameId),
            $editionId,
            Rarity::fromString($rarity),
            $imageUrl,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function nameEn(): string
    {
        return $this->nameEn;
    }

    public function namePt(): ?string
    {
        return $this->namePt;
    }

    public function cardGame(): CardGame
    {
        return $this->cardGame;
    }

    public function editionId(): string
    {
        return $this->editionId;
    }

    public function rarity(): Rarity
    {
        return $this->rarity;
    }

    public function imageUrl(): ?string
    {
        return $this->imageUrl;
    }
}
