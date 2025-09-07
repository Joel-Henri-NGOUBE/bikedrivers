<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VehiclesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Offers;
use App\Entity\Enums\Service;

final class OffersController extends AbstractController
{
     public function __invoke($user_id, $vehicle_id, Request $request, VehiclesRepository $vehiclesRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newOffer = new Offers();
        $newOffer->setTitle($payload['title']);
        $newOffer->setDescription($payload['description']);
        $newOffer->setPrice($payload['price']);
        if(in_array('service', array_keys($payload))){
            $newOffer->setService(associateService($payload['service']));
        }
        $vehiclesRepository->findOneByIdField($vehicle_id, $user_id)->addOffer($newOffer);
        $em->persist($newOffer);
        $em->flush();

        return $this->json([
            'message' => 'Offer created successfully',
        ]);

    }

}

function associateService($string){
    switch ($string) {
        case 'LOCATION':
            return Service::Location;
        case 'SALE':
            return Service::Sale;
    }
}

