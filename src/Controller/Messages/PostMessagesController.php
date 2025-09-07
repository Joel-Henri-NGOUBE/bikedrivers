<?php

namespace App\Controller\Messages;

use App\Entity\Messages;
use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PostMessagesController extends AbstractController
{
    public function __invoke($offer_id, $sender_id, $recipient_id, Request $request, OffersRepository $offersRepository, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newMessage = new Messages();
        $newMessage->setContent($payload['content']);
        $relatedOffer = $offersRepository->findOneByIdField($offer_id);
        $publisher = $relatedOffer->getVehicle()->getUser();
        if ($publisher->getId() != $sender_id && $publisher->getId() != $recipient_id) {
            return $this->json([
                'code' => '401',
                'message' => 'Neither the recipient nor the sender owns this offer',
            ])
                ->setStatusCode(401);
        }
        $relatedOffer->addMessage($newMessage);
        $userRepository->findOneByIdField($sender_id)->addMessageSent($newMessage);
        $userRepository->findOneByIdField($recipient_id)->addMessageReceived($newMessage);
        $em->persist($newMessage);
        $em->flush();

        return $this->json([
            'message' => 'Message created successfully',
        ]);

    }
}
