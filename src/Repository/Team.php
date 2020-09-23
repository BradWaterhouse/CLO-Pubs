<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;

class Team
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('
            SELECT id, name, role, bio, image
            FROM team
        ');

        $results = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if ($results) {
            return $results;
        }

        return [];
    }

    public function add(string $name, string $role, string $bio): void
    {
        $statement = $this->connection->prepare('INSERT INTO team (name, role, bio) VALUES (:name, :role, :bio)');

        $statement->execute([
            'name' => $name,
            'role' => $role,
            'bio' => $bio
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM team WHERE id = ?');
        $statement->execute([$id]);
    }
}
