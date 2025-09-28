<?php

namespace App\Controller;

use App\Entity\Pictures;
use App\Repository\UserRepository;
use App\Repository\VehiclesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PicturesController extends AbstractController
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly TokenStorageInterface $tokenStorageInterface,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(int | string $user_id, int | string $vehicle_id, Request $request, VehiclesRepository $vehiclesRepository, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse|Response
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

            // Retrieve the picture transfered in the form
            $file = $request->files->get('file');
            // var_dump($file);
            $newPicture = new Pictures();
            $newPicture->setPictureFile($file);

            $errors = $this->validator->validate($newPicture);

            if (count($errors) > 0) {
                return $this->json([
                    'error' => 'Your file is neither an image nor has a smaller size than the maximum accepted. Try uploading another image',
                ])->setStatusCode(500);
            }

            // Get the vehicule to which the picture is related
            $vehicle = $vehiclesRepository->findOneByIdField($vehicle_id, $user_id);
            $vehicle->addPicture($newPicture);
            $em->persist($newPicture);
            $em->flush();

            // Rename the picture in the database
            $newPicture->setPath('/media/pictures/' . $newPicture->getPath());
            $em->persist($newPicture);
            $em->flush();

            return $this->json([
                'message' => 'Picture successfully added',
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                'error' => $th,
            ])->setStatusCode(500);
        }
    }

    public function validate(ValidatorInterface $validator)
    {

    }
}
