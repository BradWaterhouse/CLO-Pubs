<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Applicants;
use App\Repository\Job;
use App\Repository\Pub;
use App\Repository\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Dashboard extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard", methods={"GET"})
     */
    public function index(Pub $pub, Team $team, Job $job, Applicants $applicant): Response
    {
        return $this->render('Admin/dashboard.html.twig', [
            'pubCount'=> $pub->getCount(),
            'teamCount' => $team->getCount(),
            'jobCount' => $job->getCount(),
            'applicantCount' => $applicant->getCount()
        ]);
    }
}
