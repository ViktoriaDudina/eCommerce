<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailController extends AbstractController
{
    #[Route('/send-order-mail', name: 'app_send_order_mail')]
    public function mail(MailerInterface $mailer): Response
    { $email = (new Email())

        ->from("symfony@mailer.test")
        ->to("{{user.email}}@mail.com")
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject("sent with symfony")
        ->text("hello from symfony");
        try{
        $mailer->send($email);
        return new Response("Email sent!");
    }
        catch (TransportExceptionInterface $error){
        return new Response("Error: " . $error->getMessage());

        }

    }
}
