<?php

namespace App\Controller\Documents;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DocumentsElementsController extends AbstractController
{
    public function __invoke($application_id, Request $request, DocumentsRepository $documentsRepository, EntityManagerInterface $em): JsonResponse
    {
        $documentsElements = $documentsRepository->findApplierDocumentsByApplicationId($application_id);

        return $this->json($documentsElements);

    }
}
