<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController
{
    #[Route('/notification', name: 'create_notification', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $notification = new Notification();
        $notification->setEmailRecipient($data['email_recipient']);
        $notification->setMessage($data['message']);
        $notification->setSubject($data['subject']);
        
        $entityManager->persist($notification);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Notification sent!'], JsonResponse::HTTP_CREATED);
    }
}