<?php

namespace App\Controller\Documents;

use App\Repository\DocumentsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;
final class TransferedDocumentsElementsController extends AbstractController
{
    public function __construct(private JWTTokenManagerInterface $jwtManager, private TokenStorageInterface $tokenStorageInterface){
    } 

    public function __invoke(int | string $offer_id, int | string $user_id, DocumentsRepository $documentsRepository, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        try {
            // Get the user token and decode it
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

            // Get the user id of the one authenticated
            $authenticatedUserId = $userRepository->findOneByMailField($decodedJwtToken["username"])->getId();
                
            if(!($user_id == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }
            $documentsElements = $documentsRepository->findDocumentsAssociatedToAppliedOfferByOfferAndUserId($offer_id, $user_id);

            return $this->json($documentsElements);
        } catch (\Throwable $th) {
            return $this->json([
                "error" => $th
            ])->setStatusCode(500);
        }
    }
}
