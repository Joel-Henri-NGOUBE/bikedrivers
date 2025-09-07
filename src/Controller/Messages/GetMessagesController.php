<?php

namespace App\Controller\Messages;

use App\Repository\MessagesRepository;
use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetMessagesController extends AbstractController
{
    public function __invoke($offer_id, $user_id, Request $request, MessagesRepository $messagesRepository, OffersRepository $offersRepository, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $messages = $messagesRepository->findByUserFields($offer_id, $user_id, $offersRepository->findOneByIdField($offer_id)->getVehicle()->getUser()->getId());

        return $this->json($messages);

    }
}
