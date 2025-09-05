<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RequiredDocumentsRepository;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RequiredDocuments;

final class RequiredDocumentsController extends AbstractController
{
     public function __invoke($offer_id, $required_document_id, $document_id, Request $request, RequiredDocumentsRepository $requiredDocumentsRepository, DocumentsRepository $documentsRepository, EntityManagerInterface $em): JsonResponse
    {
        // $payload = $request->getPayload()->all();
        // $newRequiredDocument = new RequiredDocuments();
        // $newRequiredDocument->setName($payload['name']);
        // $newRequiredDocument->setInformations($payload['informations']);
        $document = $documentsRepository->findOneByIdField($document_id);
        $requiredDocument = $requiredDocumentsRepository->findOneByIdField($required_document_id)
        ->addDocument($document);
        $em->flush();

        return $this->json([
            'message' => 'Document successfully added to Required Document',
        ]);

    }
}
