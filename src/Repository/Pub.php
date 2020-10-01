<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;

class Pub
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('
            SELECT id, name, town, postcode, image, stars, description
            FROM pub
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
            SELECT id, name, town, postcode, image, stars, description
            FROM pub
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
        $statement = $this->connection->query('SELECT COUNT(*) FROM pub');

        return (int) $statement->fetch(FetchMode::COLUMN);
    }

    public function add(array $pub): void
    {
        $statement = $this->connection->prepare('INSERT INTO pub (name, town, postcode, description, image) VALUES (:name, :town, :postcode, :description, :image)');

        $statement->execute([
            'name' => $pub['name'],
            'town' => $pub['town'],
            'postcode' => $pub['postcode'],
            'description' => $pub['description'],
            'image' => $pub['image']
        ]);
    }

    public function update(array $pub): void
    {
        $statement = $this->connection->prepare('
            UPDATE pub
            SET name = :name, town = :town, postcode = :postcode, description = :description, image = :image
            WHERE id = :id
       ');

        $statement->execute([
            'name' => $pub['name'],
            'town' => $pub['town'],
            'postcode' => $pub['postcode'],
            'description' => $pub['description'],
            'id' => $pub['id'],
            'image' => $pub['image']
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM pub WHERE id = ?');
        $statement->execute([$id]);
    }
}
