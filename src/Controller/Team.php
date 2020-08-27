<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Team extends AbstractController
{
    /**
     * @Route("/our-team", name="team", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Pages/team.html.twig');
    }

    /**
     * @Route("/dashboard/team", name="dashboard_team", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('Admin/admin_members.html.twig');
    }
}
