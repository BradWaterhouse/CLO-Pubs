<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Team as Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class Team extends AbstractController
{
    private SessionInterface $session;
    private Repository $repository;

    public function __construct(SessionInterface $session, Repository $repository)
    {
        $this->session = $session;
        $this->repository = $repository;
    }

    /**
     * @Route("/our-team", name="team", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Pages/team.html.twig', ['team' => $this->repository->getAll(), 'pageTitle' => 'CLO Pubs | Our Team']);
    }

    /**
     * @Route("/dashboard/team", name="dashboard_team", methods={"GET"})
     */
    public function dashboard(): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        return $this->render('Admin/admin_members.html.twig', ['team' => $this->repository->getAll()]);
    }

    /**
     * @Route("/dashboard/team/add", name="dashboard_team_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $teamMember = $request->request->all();

        if ($teamMember) {
            $this->repository->add($teamMember);

            $this->addFlash('success', 'Successfully added team member');

            return $this->redirect('/dashboard/team');
        }
    }

    /**
     * @Route("/dashboard/team/edit/{id}", name="dashboard_team_edit", methods={"GET"})
     */
    public function edit(int $id): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $teamMember = $this->repository->getById($id);

        if ($teamMember) {
            return $this->render('Admin/admin_members_edit.html.twig', ['teamMember' => $teamMember]);
        }
    }

    /**
     * @Route("/dashboard/team/update", name="dashboard_team_update", methods={"POST"})
     */
    public function update(Request $request): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        $teamMember = $request->request->all();

        if ($teamMember) {
            $this->repository->update($teamMember);

            $this->addFlash('success', 'Successfully updated team member');
            return $this->redirect('/dashboard/team/edit/' . $teamMember['id']);
        }
    }

    /**
     * @Route("/dashboard/team/delete", name="dashboard_team_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        if (!$this->session->get('logged_in')) {
            return $this->redirect('/admin');
        }

        if ($request->get('id')) {
            $this->repository->delete((int) $request->get('id'));

            $this->addFlash('success', 'Successfully deleted team member');

            return $this->redirect('/dashboard/team');
        }

        $this->addFlash('error', 'Failed To delete team member');

        return $this->redirect('/dashboard/team');
    }
}
