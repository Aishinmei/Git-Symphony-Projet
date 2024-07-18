<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'create_notification', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $notification = new Notification();
        $notification->setEmailRecipient($data['email_recipient']);
        $notification->setSujet($data['sujet']);
        $notification->setMessage($data['message']);

        $entityManager->persist($notification);
        $entityManager->flush();

        $email = (new Email())
            ->from('stevantevan04@gmail.com')
            ->to($data['email_recipient'])
            ->subject($data['sujet'])
            ->text($data['message']);

        $mailer->send($email);

        return new JsonResponse(['status' => 'Notification created and email sent!'], JsonResponse::HTTP_CREATED);
    }
}