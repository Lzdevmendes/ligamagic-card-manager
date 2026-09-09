<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\EditionRepositoryInterface;
use App\Domain\ValueObject\Edition;

final class PdoEditionRepository implements EditionRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function findByCardGame(string $cardGameId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, card_game_id, name FROM editions WHERE card_game_id = :game ORDER BY name ASC',
        );
        $stmt->execute(['game' => $cardGameId]);

        $editions = [];
        foreach ($stmt->fetchAll() as $row) {
            $editions[] = new Edition((string) $row['id'], (string) $row['card_game_id'], (string) $row['name']);
        }

        return $editions;
    }

    public function cardGameExists(string $cardGameId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM card_games WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $cardGameId]);

        return $stmt->fetch() !== false;
    }
}
