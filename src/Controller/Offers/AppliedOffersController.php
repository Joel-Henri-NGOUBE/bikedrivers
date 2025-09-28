<?php

namespace App\Controller\Offers;

use App\Repository\OffersRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

final class AppliedOffersController extends AbstractController
{
    public function __construct(private JWTTokenManagerInterface $jwtManager, private TokenStorageInterface $tokenStorageInterface){
    }
    public function __invoke(int | string $user_id, OffersRepository $offersRepository, UserRepository $userRepository): JsonResponse
    {
        try {
            // Get the user token and decode it
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

            // Get the user id of the one authenticated
            $authenticatedUserId = $userRepository->findOneByMailField($decodedJwtToken["username"])->getId();
            
            // Denying the action if the applier isn't the one requesting or an Administrator
            if(!($user_id == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }
            $appliedOffers = $offersRepository->findAppliedOffersByUserId($user_id);

            return $this->json($appliedOffers);
            
        } catch (\Throwable $th) {
            return $this->json([
                "error" => $th
            ])->setStatusCode(500);
        }
    }
}
