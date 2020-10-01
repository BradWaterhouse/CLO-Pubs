<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Pub;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class Pubs extends AbstractController
{
    private SessionInterface $session;
    private Pub $repository;

    public function __construct(SessionInterface  $session, Pub $repository)
    {
        $this->session = $session;
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
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        return $this->render('Admin/admin_pubs.html.twig', ['pubs' => $this->repository->getAll()]);
    }

    /**
     * @Route("/dashboard/pubs/add", name="dashboard_pub_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        if ($request->get('name') && $request->get('town') && $request->get('postcode') && $request->get('description')) {
            $pub = [
                'name' => $request->get('name'),
                'town' => $request->get('town'),
                'postcode' => $request->get('postcode'),
                'description' => $request->get('description'),
                'image' => $request->get('image')
            ];

            $this->repository->add($pub);

            $this->addFlash('success', 'Successfully added pub');
            return $this->redirect('/dashboard/pubs');
        }

        $this->addFlash('error', 'Failed to add pub');
        return $this->redirect('/dashboard/pubs');
    }

    /**
     * @Route("/dashboard/pub/edit/{id}", name="dashboard_pub_edit", methods={"GET"})
     */
    public function edit(int $id): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $pub = $this->repository->getById($id);

        if ($pub) {
            return $this->render('Admin/admin_pubs_edit.html.twig', ['pub' => $pub]);
        }
    }

    /**
     * @Route("/dashboard/pub/update", name="dashboard_pub_update", methods={"POST"})
     */
    public function update(Request $request): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $pub = $request->request->all();

        if ($pub) {
            $this->repository->update($pub);
            $this->addFlash('success', 'Successfully edited pub');

            return $this->redirect('/dashboard/pub/edit/' . $pub['id']);
        }
    }

    /**
     * @Route("/dashboard/pub/delete", name="dashboard_pub_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        if ($request->get('id')) {
            $this->repository->delete((int) $request->get('id'));

            $this->addFlash('success', 'Successfully deleted pub');
            return $this->redirect('/dashboard/pubs');
        }

        $this->addFlash('error', 'Failed to delete pub');
        return $this->redirect('/dashboard/pubs');
    }
}
