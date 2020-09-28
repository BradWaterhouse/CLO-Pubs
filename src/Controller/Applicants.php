<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Applicants as ApplicantsRepository;
use App\Repository\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $jobs = $this->jobs->getAll();

        return $this->render('Admin/admin_applicants.html.twig',['jobs' => $jobs]);
    }

    /**
     * @Route("dashboard/applicants/{id}", name="dashboard_applicants_by_id", methods={"GET"})
     */
    public function applicants(int $id): Response
    {
        $this->isLoggedIn();
        $applicants = $this->applicants->getApplicants($id);

        return $this->render('Admin/admin_applicants_view.html.twig', ['applicants' => $applicants, 'job' => $this->jobs->getById($id)]);
    }

    private function isLoggedIn(): ?Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        return null;
    }
}
