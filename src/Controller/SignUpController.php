<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SignUpController extends AbstractController
{
    #[Route('signup', name: 'signup', methods: ['POST'])]
    public function signup(Request $request, EntityManagerInterface $em, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $payload = $request->getPayload()->all();

        // Determines if the user exists
        $userExists = $userRepository->findOneByMailField($payload['mail']);
        // If yes renders an error response
        if ($userExists) {
            return $this->json([
                'code' => '401',
                'message' => 'An account with this email address already exists',
            ])
                ->setStatusCode(401);
        }
        // It not creates a new user account
        $newUser = new User();
        $newUser->setMail($payload['mail']);
        $newUser->setFirstname($payload['firstname']);
        $newUser->setLastname($payload['lastname']);

        // Hashes the password
        $hashedPassword = $passwordHasher->hashPassword(
            $newUser,
            $payload['password']
        );
        $userRepository->upgradePassword($newUser, $hashedPassword);

        return $this->json([
            'code' => '200',
            'message' => 'User account created successfully',
        ])
        ->setStatusCode(200);
    }

}