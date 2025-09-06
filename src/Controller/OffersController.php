<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VehiclesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Offers;

final class OffersController extends AbstractController
{
     public function __invoke($user_id, $vehicle_id, Request $request, VehiclesRepository $vehiclesRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newOffer = new Offers();
        $newOffer->setTitle($payload['title']);
        $newOffer->setDescription($payload['description']);
        $vehiclesRepository->findOneByIdField($vehicle_id, $user_id)->addOffer($newOffer);
        $em->persist($newOffer);
        $em->flush();

        return $this->json([
            'message' => 'Offer created successfully',
        ]);

    }

}

