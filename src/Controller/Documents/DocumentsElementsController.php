<?php

namespace App\Controller\Documents;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Documents;

final class DocumentsElementsController extends AbstractController
{
     public function __invoke($application_id, Request $request, DocumentsRepository $documentsRepository, EntityManagerInterface $em): JsonResponse
    {
        $documentsElements = $documentsRepository->findApplierDocumentsByApplicationId($application_id);

        return $this->json($documentsElements);

    }

}
