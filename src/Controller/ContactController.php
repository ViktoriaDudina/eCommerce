<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/handle-contact-form', name: 'app_handle_contact_form', methods: ['POST'])]
    public function handleContactForm(Request $request, MailerInterface $mailer): Response
    {
        $formData = $request->request->all();
        $recipientEmail = $formData['email'] ?? 'default@example.com';

        $email = (new Email())
            ->from('ahello4321@gmail.com') // Replace with your email
            ->to($recipientEmail) // Replace with the recipient's email
            ->subject('New Contact Form Submission')
            ->html($this->renderView('mail/contact_form.html.twig', [
                'formData' => $formData,
            ]));

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Your message has been sent successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'There was an error sending your message.');
        }

        return $this->redirectToRoute('app_contact');
    }
}

