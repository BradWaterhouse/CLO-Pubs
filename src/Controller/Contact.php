<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Contact extends AbstractController
{
    /**
     * @Route("/contact-us", name="contact", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Pages/contact.html.twig', ['pageTitle' => 'CLO Pubs | Contact']);
    }

    /**
     * @Route("/contact-us", name="contact_send", methods={"POST"})
     */
    public function send(Request $request, MailerInterface $mailer): Response
    {
        $contact = $request->request->all();
        $isValid = $this->isCsrfTokenValid('contact-form', $contact['token']);

        if ($contact && $isValid) {
            try {
                $email = (new Email())
                    ->from($_ENV['FROM_EMAIL'])
                    ->to($_ENV['TO_EMAIL'])
                    ->subject('Website Contact Form Enquiry')
                    ->html($this->formatMessage($contact));

                $mailer->send($email);

                $this->addFlash('success', 'Thank you for your email, we will be back in touch with you shortly.');
            } catch (Exception $exception) {
                $this->addFlash('error', 'We are unable to process this request at this time, please try again later.');
                return $this->redirect('/contact-us');
            }
        }

        return $this->redirect('/contact-us');
    }

    private function formatMessage(array $contact): string
    {
        return "<b>From: </b>" . strip_tags($contact['name']) . "
                <br><br>
                <b>Email: </b>" . strip_tags($contact['email']) . "
                <br><br>
                <b>Message: </b>" . $contact['message'] . "
                ";
    }
}
