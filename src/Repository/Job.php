<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;

class Job
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('
            SELECT id, title, role, town, postcode, active, description, salary
            FROM job
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
            SELECT id, title, role, town, postcode, active, description, salary
            FROM job
            WHERE id = ?
        ');

        $statement->execute([$id]);

        $results = $statement->fetch(FetchMode::ASSOCIATIVE);

        if ($results) {
            return $results;
        }

        return [];
    }

    public function getExpectations(int $id): array
    {
        $statement = $this->connection->prepare('
            SELECT expectation
            FROM job_expectations
            WHERE job_id = ?
        ');

        $statement->execute([$id]);

        $results = $statement->fetchAll(FetchMode::COLUMN);

        if ($results) {
            return $results;
        }

        return [];
    }

    public function getRequirements(int $id): array
    {
        $statement = $this->connection->prepare('
            SELECT expectation
            FROM job_expectations
            WHERE job_id = ?
        ');

        $statement->execute([$id]);

        $results = $statement->fetchAll(FetchMode::COLUMN);

        if ($results) {
            return $results;
        }

        return [];
    }
}
