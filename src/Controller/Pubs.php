<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Pub;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Pubs extends AbstractController
{
    private Pub $repository;

    public function __construct(Pub $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/our-pubs", name="pubs", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Pages/pubs.html.twig', ['pubs' => $this->repository->getAll()]);
    }

    /**
     * @Route("/dashboard/pubs", name="dashboard_pubs", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('Admin/admin_pubs.html.twig', ['pubs' => $this->repository->getAll()]);
    }

    /**
     * @Route("/dashboard/pubs/add", name="dashboard_pub_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        if ($request->get('name') && $request->get('town') && $request->get('postcode') && $request->get('description')) {

            $pub = [
                'name' => $request->get('name'),
                'town' => $request->get('town'),
                'postcode' => $request->get('postcode'),
                'description' => $request->get('description')
            ];

            $this->repository->add($pub);

            return $this->render('Admin/admin_pubs.html.twig', [
                'pubs' => $this->repository->getAll(),
                'success' => true,
                'message' => 'Team member added.'
            ]);
        }

        return $this->render('Admin/admin_pubs.html.twig', [
            'pubs' => $this->repository->getAll(),
            'success' => false,
            'message' => 'Team member could not be added.'
        ]);
    }

    /**
     * @Route("/dashboard/pub/delete", name="dashboard_pub_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        if ($request->get('id')) {
            $this->repository->delete((int) $request->get('id'));

            return $this->render('Admin/admin_pubs.html.twig', [
                'pubs' => $this->repository->getAll(),
                'success' => true,
                'message' => 'Pub deleted.'
            ]);
        }

        return $this->render('Admin/admin_pubs.html.twig', [
            'pubs' => $this->repository->getAll(),
            'success' => false,
            'message' => 'Pub could not be deleted.'
        ]);
    }
}
