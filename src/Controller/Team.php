<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Team as Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Team extends AbstractController
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/our-team", name="team", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Pages/team.html.twig', ['team' => $this->repository->getAll()]);
    }

    /**
     * @Route("/dashboard/team", name="dashboard_team", methods={"GET"})
     */
    public function dashboard(): Response
    {
        return $this->render('Admin/admin_members.html.twig', ['team' => $this->repository->getAll()]);
    }

    /**
     * @Route("/dashboard/team/add", name="dashboard_team_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        if ($request->get('name') && $request->get('role') && $request->get('bio') ) {
            $this->repository->add($request->get('name'), $request->get('role'), $request->get('bio'));

            $this->addFlash('success', 'Successfully added team member');

            return $this->redirect('/dashboard/team');
        }
    }

    /**
     * @Route("/dashboard/team/edit/{id}", name="dashboard_team_edit", methods={"GET"})
     */
    public function edit(int $id): Response
    {
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
        if ($request->get('id')) {
            $this->repository->delete((int) $request->get('id'));

            $this->addFlash('success', 'Successfully deleted team member');

            return $this->redirect('/dashboard/team');
        }

        $this->addFlash('error', 'Failed To delete team member');

        return $this->redirect('/dashboard/team');
    }
}
