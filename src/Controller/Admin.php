<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class Admin extends AbstractController
{
    private SessionInterface $session;
    private User $user;

    public function __construct(SessionInterface $session, User $user)
    {
        $this->session = $session;
        $this->user = $user;
    }

    /**
     * @Route("/admin", name="admin", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Pages/admin.html.twig');
    }

    /**
     * @Route("/admin", name="admin_submit", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $token = $request->get('token') ?? '';



        if ($username && $password && $this->isCsrfTokenValid('login-form', $token)) {
            $loggedIn = $this->loginByUsername($username, $password);

            if ($loggedIn) {
               return $this->redirect('/dashboard');
            }
        }

        return $this->redirect('/admin');
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout(): Response
    {
        $this->session->remove('logged_in');

        return $this->redirect('admin');
    }

    private function loginByUsername(string $username, string $password): bool
    {
        if (\trim($password) === '') {
            return false;
        }

        $user = $this->user->getUserByName($username);

        if ($user && hash('sha256', $password . $_ENV['SALT']) === $user['password']) {
            return $this->setLoggedIn($user);
        }

        return false;
    }

    private function setLoggedIn(array $user): bool
    {
        $this->session->set('userId', (int) $user['id']);
        $this->session->set('username', $user['username']);
        $this->session->set('logged_in', true);

        return $this->session->get('logged_in', false);
    }
}
