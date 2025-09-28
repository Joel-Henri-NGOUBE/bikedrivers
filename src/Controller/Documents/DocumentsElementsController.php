<?php

namespace App\Controller\Documents;

use App\Repository\DocumentsRepository;
use App\Repository\UserRepository;
use App\Repository\ApplicationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

final class DocumentsElementsController extends AbstractController
{
    public function __construct(private JWTTokenManagerInterface $jwtManager, private TokenStorageInterface $tokenStorageInterface){
    }
    public function __invoke(int | string $application_id, DocumentsRepository $documentsRepository, UserRepository $userRepository, ApplicationsRepository $applicationsRepository): JsonResponse
    {
        try {
            // Get the user token and decode it
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

            // Get the user id of the one authenticated
            $authenticatedUserId = $userRepository->findOneByMailField($decodedJwtToken["username"])->getId();
            // Get the id of the one owning the offer

            $offerOwnerId = $applicationsRepository->findOneById($application_id)->getOffer()->getVehicle()->getUser()->getId();
            // Denying the action if the owner isn't the one requesting or an Administrator

            if(!($offerOwnerId == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }
            $documentsElements = $documentsRepository->findApplierDocumentsByApplicationId($application_id);

            return $this->json($documentsElements);
        } catch (\Throwable $th) {
            return $this->json([
                "error" => $th
            ])->setStatusCode(500);
        }
    }
}
