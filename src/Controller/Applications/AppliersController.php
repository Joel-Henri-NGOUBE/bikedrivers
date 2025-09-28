<?php

namespace App\Controller\Applications;

use App\Repository\ApplicationsRepository;
use App\Repository\UserRepository;
use App\Repository\OffersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

final class AppliersController extends AbstractController
{
    public function __construct(private JWTTokenManagerInterface $jwtManager, private TokenStorageInterface $tokenStorageInterface){
    }
    public function __invoke(int | string $offer_id, ApplicationsRepository $applicationsRepository, UserRepository $userRepository, OffersRepository $offersRepository): JsonResponse
    {
        try {
            // Get the user token and decode it
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

            // Get the user id of the one authenticated
            $authenticatedUserId = $userRepository->findOneByMailField($decodedJwtToken["username"])->getId();
            
            // Get the id of the one owning the offer
            $offerOwnerId = $offersRepository->findOneByIdField($offer_id)->getVehicle()->getUser()->getId();

            // Denying the action if the owner isn't the one requesting or an Administrator
            if(!($offerOwnerId == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }
            $appliers = $applicationsRepository->findAppliersByOfferId($offer_id);

            return $this->json($appliers);
        } catch (\Throwable $th) {
            return $this->json([
                "error" => $th
            ])->setStatusCode(500);
        }
    }
}
