<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Job;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Jobs extends AbstractController
{
    private Job $repository;

    public function __construct(Job $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/jobs", name="jobs", methods={"GET"})
     */
    public function index(): Response
    {
        $formatted = [];

        foreach ($this->repository->getAll() as $job) {
            $formatted[] = [
                'job' => $job,
                'expectations' => $this->repository->getExpectations((int) $job['id']),
                'requirements' => $this->repository->getRequirements((int) $job['id'])
            ];
        }

       return $this->render('Pages/jobs.html.twig', ['jobs' => $formatted]);
    }

    /**
     * @Route("/job/{id}/apply", name="apply", methods={"GET"})
     */
    public function apply(int $id): Response
    {
        $job = $this->repository->getById($id);

        if ($job) {
            return $this->render('Pages/apply.html.twig', ['job' => $job]);
        }

        return $this->render('Pages/home.html.twig');
    }

    /**
     * @Route("/job/send", name="send", methods={"POST"})
     */
    public function send(Request $request): Response
    {
        $application = $request->request->all();

        $this->repository->apply($application);

        return $this->render('Pages/applied.html.twig');
    }

    /**
     * @Route("/dashboard/jobs", name="dashboard_jobs", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('Admin/admin_jobs.html.twig', ['jobs' => $this->repository->getAll()]);
    }

    /**
     * @Route("/dashboard/job/add", name="dashboard_job_add", methods={"POST", "GET"})
     */
    public function add(Request $request): Response
    {
        try {
            $job = $request->request->all();
            $jobId = $this->repository->add($job);

            $expectations = \explode("\n", $job['expectations']);
            $requirements = \explode("\n", $job['requirements']);

            foreach ($expectations as $expectation) {
                $this->repository->addExpectation($jobId, $expectation);
            }

            foreach ($requirements as $requirement) {
                $this->repository->addRequirement($jobId, $requirement);
            }

            return $this->render('Admin/admin_jobs.html.twig', [
                'jobs' => $this->repository->getAll(),
                'success' => true,
                'message' => 'Job advert added.'
            ]);

        } catch (Exception $exception) {
            return $this->render('Admin/admin_jobs.html.twig', [
                'jobs' => $this->repository->getAll(),
                'success' => false,
                'message' => 'Unable to add job advert.'
            ]);
        }
    }

    /**
     * @Route("/dashboard/job/delete", name="dashboard_job_delete", methods={"POST", "GET"})
     */
    public function delete(Request $request): Response
    {
        $this->repository->delete((int) $request->get('id'));

        return $this->render('Admin/admin_jobs.html.twig', [
            'jobs' => $this->repository->getAll(),
            'success' => true,
            'message' => 'Job advert deleted.'
        ]);
    }
}
