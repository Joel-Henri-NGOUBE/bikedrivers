<?php

namespace App\Controller\Documents;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class TransferedDocumentsElementsController extends AbstractController
{
    public function __invoke($offer_id, $user_id, Request $request, DocumentsRepository $documentsRepository, EntityManagerInterface $em): JsonResponse
    {
        $documentsElements = $documentsRepository->findDocumentsAssociatedToAppliedOfferByOfferAndUserId($offer_id, $user_id);

        return $this->json($documentsElements);

    }
}
