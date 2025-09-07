<?php

namespace App\Controller;

use App\Repository\DocumentsRepository;
use App\Repository\RequiredDocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class RequiredDocumentsController extends AbstractController
{
    public function __invoke($offer_id, $required_document_id, $document_id, Request $request, RequiredDocumentsRepository $requiredDocumentsRepository, DocumentsRepository $documentsRepository, EntityManagerInterface $em): JsonResponse
    {
        $document = $documentsRepository->findOneByIdField($document_id);
        $requiredDocument = $requiredDocumentsRepository->findOneByIdField($required_document_id)
            ->addDocument($document);
        $em->flush();

        return $this->json([
            'message' => 'Document successfully added to Required Document',
        ]);

    }
}
