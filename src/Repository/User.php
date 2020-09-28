<?php

declare(strict_types=1);

namespace App\Repository;


use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;

class User
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getUserByName(string $username): array
    {
        $statement = $this->connection->prepare('SELECT id, password, username FROM user WHERE username = ?');

        $statement->execute([$username]);

        $results = $statement->fetch(FetchMode::ASSOCIATIVE);

        if ($results) {
            return $results;
        }

        return [];
    }
}
