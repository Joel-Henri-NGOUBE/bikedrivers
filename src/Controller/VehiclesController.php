<?php

namespace App\Controller;

use App\Entity\Vehicles;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class VehiclesController extends AbstractController
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly TokenStorageInterface $tokenStorageInterface
    ) {
    }

    public function __invoke(int | string $user_id, Request $request, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        try {
            // Get the user token and decode it
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

            // Get the user id of the one authenticated
            $authenticatedUserId = $userRepository->findOneByMailField($decodedJwtToken['username'])->getId();

            // Denying the action if the vehicle owner isn't the one requesting or an Administrator
            if (! ($user_id == $authenticatedUserId || in_array('ROLE_ADMIN', $decodedJwtToken['roles']))) {
                throw new Exception("You are not allowed to act on someone else's data");
            }

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

        } catch (\Throwable $th) {
            return $this->json([
                'error' => $th,
            ])->setStatusCode(500);
        }

    }
}
