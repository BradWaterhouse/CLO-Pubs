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

            $this->addFlash('success', 'Successfully added job advert');

            return $this->redirect('/dashboard/jobs');

        } catch (Exception $exception) {
            $this->addFlash('error', 'Failed to add job advert');

            return $this->redirect('/dashboard/jobs');
        }
    }

    /**
     * @Route("/dashboard/job/edit/{id}", name="dashboard_job_edit", methods={"GET"})
     */
    public function edit(int $id): Response
    {
        $job = $this->formatChecked($this->repository->getById($id));

        if ($job) {
            $formatted = [
                'job' => $job,
                'expectations' => \implode("\n", $this->repository->getExpectations($id)),
                'requirements' => \implode("\n", $this->repository->getRequirements($id))
                ];

            return $this->render('Admin/admin_jobs_edit.html.twig', ['job' => $formatted]);
        }

        return $this->redirect('dashboard/jobs');
    }

    /**
     * @Route("/dashboard/job/update", name="dashboard_job_update", methods={"POST"})
     */
    public function updateJob(Request $request): Response
    {
        $job = $request->request->all();

        if ($job) {
            $this->repository->updateJob($job);

            $this->addFlash('success', 'Successfully updated job');
            return $this->redirect('/dashboard/job/edit/' . $job['id']);
        }

        $this->addFlash('error', 'Failed to update job');
        return $this->redirect('/dashboard/job/edit/' . $job['id']);
    }

    /**
     * @Route("/dashboard/job/requirements/update", name="dashboard_job_update_requirements", methods={"POST"})
     */
    public function updateRequirements(Request $request): Response
    {
        $requirements = $request->get('requirements');
        $jobId = (int) $request->get('id');

        $requirements = \explode("\n", $requirements);

        if ($requirements && $jobId) {
            $this->repository->deleteRequirements($jobId);
            foreach ($requirements as $requirement) {
                $this->repository->addRequirement($jobId, $requirement);
            }

            $this->addFlash('success', 'Successfully updated job requirements');
            return $this->redirect('/dashboard/job/edit/' . $jobId);
        }

        $this->addFlash('error', 'Failed to update job requirements');

        return $this->redirect('/dashboard/job/edit/' . $jobId);
    }

    /**
     * @Route("/dashboard/job/expectations/update", name="dashboard_job_update_expectations", methods={"POST"})
     */
    public function updateExpectations(Request $request): Response
    {
        $expectations = $request->get('expectations');
        $jobId = (int) $request->get('id');

        $expectations =\explode("\n", $expectations);

        if ($expectations && $jobId) {
            $this->repository->deleteExpectations($jobId);
            foreach ($expectations as $expectation) {
                $this->repository->addExpectation($jobId, $expectation);
            }

            $this->addFlash('success', 'Successfully updated job expectations');
            return $this->redirect('/dashboard/job/edit/' . $jobId);
        }

        $this->addFlash('error', 'Failed to update job requirements');

        return $this->redirect('/dashboard/job/edit/' . $jobId);
    }

    /**
     * @Route("/dashboard/job/delete", name="dashboard_job_delete", methods={"POST", "GET"})
     */
    public function delete(Request $request): Response
    {
        $this->repository->delete((int) $request->get('id'));

        $this->addFlash('success', 'Successfully deleted job advert');

        return $this->redirect('/dashboard/jobs');
    }

    private function formatChecked(array $job): array
    {
        if ($job) {
            if ($job['active'] === '1') {
                $job['active'] = 'checked';
            } else {
                $job['active'] = '';
            }

            return $job;
        }

        return [];

    }
}
