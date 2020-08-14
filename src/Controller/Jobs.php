<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Jobs extends AbstractController
{
    /**
     * @Route("/jobs", name="jobs", methods={"GET"})
     */
    public function index(): void
    {
        $this->render('pages/jobs.html.twig');
    }
}
