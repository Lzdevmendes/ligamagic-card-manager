<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Card;
use App\Domain\Repository\CardRepositoryInterface;

final class PdoCardRepository implements CardRepositoryInterface
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, name_en, name_pt, card_game_id, edition_id, rarity, image_url
             FROM cards ORDER BY id DESC',
        );

        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    public function findById(int $id): ?Card
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name_en, name_pt, card_game_id, edition_id, rarity, image_url
             FROM cards WHERE id = :id LIMIT 1',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function insert(Card $card): Card
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO cards (name_en, name_pt, card_game_id, edition_id, rarity, image_url)
             VALUES (:name_en, :name_pt, :card_game_id, :edition_id, :rarity, :image_url)',
        );
        $stmt->execute($this->bindings($card));

        return $this->findById((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, Card $card): Card
    {
        $stmt = $this->pdo->prepare(
            'UPDATE cards SET name_en = :name_en, name_pt = :name_pt, card_game_id = :card_game_id,
             edition_id = :edition_id, rarity = :rarity, image_url = :image_url WHERE id = :id',
        );
        $stmt->execute([...$this->bindings($card), 'id' => $id]);

        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM cards WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function editionExists(string $editionId, string $cardGameId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM editions WHERE id = :edition_id AND card_game_id = :card_game_id LIMIT 1',
        );
        $stmt->execute(['edition_id' => $editionId, 'card_game_id' => $cardGameId]);

        return $stmt->fetch() !== false;
    }

    /** @return array<string, mixed> */
    private function bindings(Card $card): array
    {
        return [
            'name_en' => $card->nameEn(),
            'name_pt' => $card->namePt(),
            'card_game_id' => $card->cardGame()->value(),
            'edition_id' => $card->editionId(),
            'rarity' => $card->rarity()->value(),
            'image_url' => $card->imageUrl(),
        ];
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Card
    {
        return Card::fromPersistence(
            (int) $row['id'],
            (string) $row['name_en'],
            $row['name_pt'] !== null ? (string) $row['name_pt'] : null,
            (string) $row['card_game_id'],
            (string) $row['edition_id'],
            (string) $row['rarity'],
            $row['image_url'] !== null ? (string) $row['image_url'] : null,
        );
    }
}
