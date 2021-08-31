<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;

class Team
{
    CONST DEFAULT_IMAGE = 'https://st4.depositphotos.com/1156795/20814/v/450/depositphotos_208142514-stock-illustration-profile-placeholder-image-gray-silhouette.jpg';
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('
            SELECT id, name, role, image
            FROM team
        ');

        $results = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if ($results) {
            return $results;
        }

        return [];
    }

    public function getById(int $id): array
    {
        $statement = $this->connection->prepare('
            SELECT id, name, role, image
            FROM team
            WHERE id = ?
        ');

        $statement->execute([$id]);

        $results = $statement->fetch(FetchMode::ASSOCIATIVE);

        if ($results) {
            return $results;
        }

        return [];
    }

    public function getCount(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM team');

        return (int) $statement->fetch(FetchMode::COLUMN);
    }

    public function add(array $teamMember): void
    {
        $statement = $this->connection->prepare('INSERT INTO team (name, role, image) VALUES (:name, :role, :image)');

        $statement->execute([
            'name' => $teamMember['name'],
            'role' => $teamMember['role'],
            'image' => (!empty($teamMember['image']) ? $teamMember['image'] : self::DEFAULT_IMAGE)
        ]);
    }

    public function update(array $teamMember): void
    {
        $statement = $this->connection->prepare('UPDATE team SET name = :name, role = :role, image = :image WHERE id = :id');

        $statement->execute([
            'name' => $teamMember['name'],
            'role' => $teamMember['role'],
            'id' => $teamMember['id'],
            'image' => $teamMember['image']
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM team WHERE id = ?');
        $statement->execute([$id]);
    }
}
