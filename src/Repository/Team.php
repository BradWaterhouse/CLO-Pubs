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
}
