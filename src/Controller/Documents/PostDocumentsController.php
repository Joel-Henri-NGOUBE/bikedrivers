<?php

namespace App\Controller\Documents;

use App\Entity\Documents;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PostDocumentsController extends AbstractController
{
    public function __invoke(int | string $user_id, Request $request, userRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
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

    }
}
