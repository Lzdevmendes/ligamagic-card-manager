<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class Edition
{
    public function __construct(
        private readonly string $id,
        private readonly string $cardGameId,
        private readonly string $name,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function cardGameId(): string
    {
        return $this->cardGameId;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return array{id:string, name:string} */
    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name];
    }
}
