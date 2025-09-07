<?php

namespace App\Controller\Messages;

use App\Repository\MessagesRepository;
use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PatchMessageController extends AbstractController
{
    public function __invoke($offer_id, $user_id, $message_id, Request $request, MessagesRepository $messagesRepository, OffersRepository $offersRepository, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        return $this->json($messagesRepository->setContentById($message_id, $payload['content']));

    }
}
