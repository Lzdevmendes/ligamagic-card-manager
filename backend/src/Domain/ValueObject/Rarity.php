<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\ValidationException;

final class Rarity
{
    private const VALID_VALUES = ['common', 'uncommon', 'rare', 'mythic', 'secret'];

    private function __construct(private readonly string $value)
    {
    }

    public static function fromString(string $value): self
    {
        if (!in_array($value, self::VALID_VALUES, true)) {
            throw new ValidationException([
                'rarity' => 'Raridade inválida. Use uma de: ' . implode(', ', self::VALID_VALUES) . '.',
            ]);
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    /** @return string[] */
    public static function values(): array
    {
        return self::VALID_VALUES;
    }
}
