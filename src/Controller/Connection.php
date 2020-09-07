<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Driver\Connection as DoctrineConnection;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Connection
{
    private $connection;

    public function __construct(DoctrineConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @Route("/connection-test", name="connection", methods={"GET"})
     */
    public function index(): Response
    {
        $statement = $this->connection->query('SHOW TABLES');

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        return new JsonResponse($results);
    }
}