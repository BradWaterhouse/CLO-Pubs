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
            SELECT id, name, town, postcode, image, stars
            FROM pub
        ');

        $results = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if ($results) {
            return $results;
        }

        return [];
    }
}
