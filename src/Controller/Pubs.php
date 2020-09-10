<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Pub;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Pubs extends AbstractController
{
    /**
     * @Route("/our-pubs", name="pubs", methods={"GET"})
     */
    public function index(Pub $repository): Response
    {
        return $this->render('Pages/pubs.html.twig', ['pubs' => $repository->getAll()]);
    }

    /**
     * @Route("/dashboard/pubs", name="dashboard_pubs", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('Admin/admin_pubs.html.twig');
    }
}
