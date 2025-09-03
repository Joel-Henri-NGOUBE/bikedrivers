<?php

namespace App\Controller\Messages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use App\Repository\MessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Messages;

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

