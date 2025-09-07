<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class CommentsController extends AbstractController
{
    public function __invoke($user_id, $offer_id, Request $request, UserRepository $userRepository, OffersRepository $offersRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newComment = new Comments();
        $newComment->setContent($payload['content']);
        $userRepository->findOneByIdField($user_id)->addComment($newComment);
        $offersRepository->findOneByIdField($offer_id)->addComment($newComment);
        $em->persist($newComment);
        $em->flush();

        return $this->json([
            'message' => 'Comment created successfully',
        ]);

    }
}
