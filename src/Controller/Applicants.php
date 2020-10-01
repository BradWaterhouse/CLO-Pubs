<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Applicants as ApplicantsRepository;
use App\Repository\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class Applicants extends AbstractController
{
    private SessionInterface $session;
    private ApplicantsRepository $applicants;
    private Job $jobs;

    public function __construct(SessionInterface $session, ApplicantsRepository $applicants, Job $jobs)
    {
        $this->session = $session;
        $this->applicants = $applicants;
        $this->jobs = $jobs;
    }

    /**
     * @Route("dashboard/applicants", name="dashboard_applicants", methods={"GET"})
     */
    public function index(): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $jobs = $this->jobs->getAll();

        return $this->render('Admin/admin_applicants.html.twig',['jobs' => $jobs]);
    }

    /**
     * @Route("dashboard/applicants/{id}", name="dashboard_applicants_by_id", methods={"GET"})
     */
    public function applicants(int $id): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $applicants = $this->applicants->get($id);

        return $this->render('Admin/admin_applicants_view.html.twig', ['applicants' => $applicants, 'job' => $this->jobs->getById($id)]);
    }

    /**
     * @Route("dashboard/applicant/delete", name="dashboard_applicants_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $id = $request->get('id');
        $jobId = $request->get('job_id');

        if ($id && $jobId) {
            $this->applicants->delete((int) $id);
            return $this->redirect('/dashboard/applicants/' . $jobId);
        }

        return $this->redirect('/dashboard');
    }
}
