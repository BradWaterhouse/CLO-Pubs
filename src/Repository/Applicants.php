<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;

class Applicants
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get(int $jobId): array
    {
        $statement = $this->connection->prepare('
            SELECT id, job_id, email, name, question_one, question_two, question_three, question_four, contact_number
            FROM job_applicants
            WHERE job_id = ?
        ');

        $statement->execute([$jobId]);

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    public function getCount(): int
    {
        $statement = $this->connection->query('
            SELECT COUNT(*)
            FROM job_applicants
        ');

        return (int) $statement->fetch(FetchMode::COLUMN);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM job_applicants WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
