<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RequiredDocumentsRepository;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MatchDocuments;
use App\Entity\Enums\Status;

final class MatchDocumentsController extends AbstractController
{
     public function __invoke($required_document_id, $document_id, Request $request, RequiredDocumentsRepository $requiredDocumentRepository, DocumentsRepository $documentsRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newMatchDocument = new MatchDocuments();
        $requiredDocumentRepository->findOneByIdField($required_document_id)->addMatchDocument($newMatchDocument);
        $documentsRepository->findOneByIdField($document_id)->addMatchDocument($newMatchDocument);
        $em->persist($newMatchDocument);
        $em->flush();

        return $this->json([
            'message' => 'Document successfully matched to required one created',
        ]);

    }

}