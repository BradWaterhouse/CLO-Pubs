<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Admin extends AbstractController
{
    /**
     * @Route("/admin", name="admin", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Pages/admin.html.twig');
    }
}
