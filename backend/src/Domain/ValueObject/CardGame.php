<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\ValidationException;

final class CardGame
{
    private const VALID_IDS = ['magic', 'pokemon', 'yugioh'];

    private function __construct(private readonly string $id)
    {
    }

    public static function fromString(string $id): self
    {
        if (!in_array($id, self::VALID_IDS, true)) {
            throw new ValidationException([
                'card_game' => 'Card Game inválido. Use um de: ' . implode(', ', self::VALID_IDS) . '.',
            ]);
        }

        return new self($id);
    }

    public function value(): string
    {
        return $this->id;
    }
}
