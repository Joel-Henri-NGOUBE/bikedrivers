<?php

namespace App\Controller;

use App\Entity\MatchDocuments;
use App\Repository\DocumentsRepository;
use App\Repository\RequiredDocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class MatchDocumentsController extends AbstractController
{
    public function __invoke(int | string $required_document_id, int | string $document_id, RequiredDocumentsRepository $requiredDocumentRepository, DocumentsRepository $documentsRepository, EntityManagerInterface $em): JsonResponse
    {
        $newMatchDocument = new MatchDocuments();
        $requiredDocumentRepository->findOneByIdField($required_document_id)->addMatchDocument($newMatchDocument);
        // Linking the document with the requiredDocument
        $documentsRepository->findOneByIdField($document_id)->addMatchDocument($newMatchDocument);
        $em->persist($newMatchDocument);
        $em->flush();

        return $this->json([
            'message' => 'Document successfully matched to required one created',
        ]);

    }
}
