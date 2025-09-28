<?php

namespace App\Controller\Applications;

use App\Repository\ApplicationsRepository;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class HasAppliedController extends AbstractController
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly TokenStorageInterface $tokenStorageInterface
    ) {
    }

    public function __invoke(int | string $offer_id, int | string $user_id, ApplicationsRepository $applicationsRepository, UserRepository $userRepository): JsonResponse
    {
        try {
            // Get the user token and decode it
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

            // Get the user id of the one authenticated
            $authenticatedUserId = $userRepository->findOneByMailField($decodedJwtToken['username'])->getId();

            // Denying the action if the applier isn't the one requesting or an Administrator
            if (! ($user_id == $authenticatedUserId || in_array('ROLE_ADMIN', $decodedJwtToken['roles']))) {
                throw new Exception("You are not allowed to act on someone else's data");
            }

            $user = $applicationsRepository->findIfUserHasApplied($offer_id, $user_id);

            if (count($user)) {
                return $this->json([
                    'hasApplied' => true,
                ]);
            }

            return $this->json([
                'hasApplied' => false,
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                'error' => $th,
            ])->setStatusCode(500);
        }
    }
}
