<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Jobs extends AbstractController
{
    /**
     * @Route("/jobs", name="jobs", methods={"GET"})
     */
    public function index(): Response
    {
       return $this->render('Pages/jobs.html.twig');
    }

    /**
     * @Route("/job/{id}/apply", name="apply", methods={"GET"})
     */
    public function apply(): Response
    {
        return $this->render('Pages/apply.html.twig');
    }
}
