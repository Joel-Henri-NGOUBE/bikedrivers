<?php

namespace App\Controller;

use App\Entity\Vehicles;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class VehiclesController extends AbstractController
{
    public function __invoke($user_id, Request $request, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $newVehicle = new Vehicles();
        $newVehicle->setType($payload['type']);
        $newVehicle->setModel($payload['model']);
        $newVehicle->setBrand($payload['brand']);
        $newVehicle->setPurchasedAt(new \DateTimeImmutable($payload['purchasedAt']));
        $userRepository->findOneByIdField($user_id)->addVehicle($newVehicle);
        $em->persist($newVehicle);
        $em->flush();

        return $this->json([
            'message' => 'Vehicle created successfully',
        ]);

    }
}
