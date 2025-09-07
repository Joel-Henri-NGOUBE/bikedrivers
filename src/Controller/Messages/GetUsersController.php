<?php

namespace App\Controller\Messages;

use App\Repository\MessagesRepository;
use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetUsersController extends AbstractController
{
    public function __invoke($offer_id, Request $request, MessagesRepository $messagesRepository, OffersRepository $offersRepository, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $relatedOffer = $offersRepository->findOneByIdField($offer_id);
        $publisher = $relatedOffer->getVehicle()->getUser();
        $messages = $messagesRepository->findUsersByIdAndOfferIdFields($offer_id, $publisher->getId());
        return $this->json($messages);

    }
}
