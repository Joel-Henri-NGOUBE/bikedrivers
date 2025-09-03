<?php

namespace App\Controller\Messages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use App\Repository\MessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Messages;

final class DeleteMessageController extends AbstractController
{
     public function __invoke($offer_id, $user_id, $message_id, Request $request, MessagesRepository $messagesRepository, OffersRepository $offersRepository, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        // Enlever le message de l'offre et des utilisateurs
        $messagesRepository->deleteByIdField($message_id);


        return (new Response())->setStatusCode(204);

    }

}

