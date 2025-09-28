<?php

namespace App\Controller\Documents;

use App\Entity\Documents;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

final class PostDocumentsController extends AbstractController
{
    public function __construct(private JWTTokenManagerInterface $jwtManager, private TokenStorageInterface $tokenStorageInterface){
    }
    public function __invoke(int | string $user_id, Request $request, userRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        try {
            // Get the user token and decode it     
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

            // Get the user id of the one authenticated
            $authenticatedUserId = $userRepository->findOneByMailField($decodedJwtToken["username"])->getId();
                
            if(!($user_id == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }

            // Retrieve the Document transfered in the form
            $file = $request->files->get('file');
            $newDocument = new Documents();
            $newDocument->setDocumentFile($file);

            // Get the vehicule to which the Document is related
            $user = $userRepository->findOneByIdField($user_id);
            $user->addDocument($newDocument);
            $em->persist($newDocument);
            $em->flush();

            // Rename the Document in the database
            $newDocument->setPath('/media/documents/' . $newDocument->getPath());
            $em->persist($newDocument);
            $em->flush();

            return $this->json([
                'message' => 'Document successfully added',
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                "error" => $th
            ])->setStatusCode(500);
        }
    }
}
