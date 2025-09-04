<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RequiredDocuments;

final class RequiredDocumentsController extends AbstractController
{
     public function __invoke($offer_id, Request $request, offersRepository $offersRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newRequiredDocument = new RequiredDocuments();
        $newRequiredDocument->setName($payload['name']);
        $newRequiredDocument->setInformations($payload['informations']);
        $offersRepository->findOneByIdField($offer_id)->addRequiredDocument($newRequiredDocument);
        $em->persist($newRequiredDocument);
        $em->flush();

        return $this->json([
            'message' => 'Required Document successfully added',
        ]);

    }
}
