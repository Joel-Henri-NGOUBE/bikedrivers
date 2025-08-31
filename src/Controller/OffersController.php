<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VehiclesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Offers;
use App\Entity\Enums\Status;

final class OffersController extends AbstractController
{
     public function __invoke($user_id, $vehicle_id, Request $request, VehiclesRepository $vehiclesRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newOffer = new Offers();
        $newOffer->setDescription($payload['description']);
        $newOffer->setStatus(associateStatus($payload['status']));
        $vehiclesRepository->findOneByIdField($vehicle_id, $user_id)->addOffer($newOffer);
        $em->persist($newOffer);
        $em->flush();

        return $this->json([
            'message' => 'Offer created successfully',
        ]);

    }

}
function associateStatus($string){
    switch ($string) {
        case 'AVAILABLE':
            return Status::Available;
        case 'BOUGHT':
            return Status::Bought;
        case 'RENTED':
            return Status::Rented;
        case 'INACTIVE':
            return Status::Inactive;
    }
}
