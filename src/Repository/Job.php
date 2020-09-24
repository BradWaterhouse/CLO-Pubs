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
            WHERE active = 1
        ');

        $results = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if ($results) {
            return $results;
        }

        return [];
    }

    public function getCount(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM job');

        return (int) $statement->fetch(FetchMode::COLUMN);
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
            SELECT requirement
            FROM job_requirements
            WHERE job_id = ?
        ');

        $statement->execute([$id]);

        $results = $statement->fetchAll(FetchMode::COLUMN);

        if ($results) {
            return $results;
        }

        return [];
    }

    public function apply(array $application): void
    {
        $statement = $this->connection->prepare('
            INSERT INTO job_applicants (job_id, name, email, question_one, question_two, question_three, question_four)
            VALUES (:job_id, :name, :email, :question_one, :question_two, :question_three, :question_four)
       ');

        $statement->execute([
            'job_id' => (int) $application['id'],
            'name' => $application['name'],
            'email' => $application['email'],
            'question_one' => $application['personal_licence'],
            'question_two' => $application['cellar_knowledge'],
            'question_three' => $application['experience'],
            'question_four' => $application['relocate']
        ]);
    }

    public function add(array $job): int
    {
        $statement = $this->connection->prepare('
            INSERT INTO job (title, role, town, postcode, description, active, salary)
            VALUES (:name, :title, :town, :postcode, :description, :active, :salary);
        ');

        $statement->execute([
            'name' => $job['name'],
            'title' => $job['title'],
            'town' => $job['town'],
            'postcode' => $job['postcode'],
            'description' => $job['description'],
            'active' => self::isActive($job['active']),
            'salary' => $job['salary']
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function addExpectation(int $jobId, string $expectation): void
    {
        $statement = $this->connection->prepare('INSERT INTO job_expectations (job_id, expectation) VALUES (:job_id, :expectation)');

        $statement->execute([
            'job_id' => $jobId,
            'expectation' => $expectation
        ]);
    }

    public function addRequirement(int $jobId, string $requirement): void
    {
        $statement = $this->connection->prepare('INSERT INTO job_requirements (job_id, requirement) VALUES (:job_id, :requirement)');

        $statement->execute([
            'job_id' => $jobId,
            'requirement' => $requirement
        ]);
    }

    public function updateJob(array $job): void
    {
        $statement = $this->connection->prepare('
            UPDATE job
            SET
            title = :name,
            role = :title,
            town = :town,
            postcode = :postcode,
            description = :description,
            active = :active,
            salary = :salary
            WHERE id = :id
        ');

        $statement->execute([
            'name' => $job['name'],
            'title' => $job['title'],
            'town' => $job['town'],
            'postcode' => $job['postcode'],
            'description' => $job['description'],
            'active' => (\array_key_exists('active', $job) ? 1 : 0),
            'salary' => $job['salary'],
            'id' => $job['id']
        ]);
    }

    public function delete(int $jobId): void
    {
        $this->deleteExpectations($jobId);
        $this->deleteRequirements($jobId);
        $this->deleteApplications($jobId);

        $statement = $this->connection->prepare('DELETE FROM job WHERE id = ?');
        $statement->execute([$jobId]);
    }

    private static function isActive(string $input): int
    {
        if ($input === 'on') {
            return 1;
        }

        return 0;
    }

    public function deleteExpectations(int $jobId): void
    {
        $statement = $this->connection->prepare('DELETE FROM job_expectations WHERE job_id = ?');
        $statement->execute([$jobId]);
    }

    public function deleteRequirements(int $jobId): void
    {
        $statement = $this->connection->prepare('DELETE FROM job_requirements WHERE job_id = ?');
        $statement->execute([$jobId]);
    }

    private function deleteApplications(int $jobId): void
    {
        $statement = $this->connection->prepare('DELETE FROM job_applicants WHERE job_id = ?');
        $statement->execute([$jobId]);
    }
}
