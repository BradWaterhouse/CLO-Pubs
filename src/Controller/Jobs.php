<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Jobs extends AbstractController
{
    /**
     * @Route("/jobs", name="jobs", methods={"GET"})
     */
    public function index(Job $repository): Response
    {
        $formatted = [];

        foreach ($repository->getAll() as $job) {
            $formatted[] = [
                'job' => $job,
                'expectations' => $repository->getExpectations((int) $job['id']),
                'requirements' => $repository->getRequirements((int) $job['id'])
            ];
        }

       return $this->render('Pages/jobs.html.twig', ['jobs' => $formatted]);
    }

    /**
     * @Route("/job/{id}/apply", name="apply", methods={"GET"})
     */
    public function apply(int $id, Job $repository): Response
    {
        $job = $repository->getById($id);

        if ($job) {
            return $this->render('Pages/apply.html.twig', ['job' => $job]);
        }

        return $this->render('Pages/home.html.twig');
    }
}
