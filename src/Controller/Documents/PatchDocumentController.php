<?php

namespace App\Controller\Documents;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Documents;
use App\Entity\Enums\State;

final class PatchDocumentController extends AbstractController
{
     public function __invoke($user_id, $document_id, Request $request, DocumentsRepository $documentsRepository, userRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $payload = $request->getPayload()->all();

        $document = $documentsRepository->findOneById($document_id);

        $document->setState(associateState($payload['state']));
        $document->setAddedAt();
        $em->persist($document);
        $em->flush();

        return $this->json($document);
        // // Retrieve the Document transfered in the form
        // $file = $request->files->get('file');
        // $newDocument = new Documents();
        // $newDocument->setDocumentFile($file);
        // $newDocument->setState(State::Unevaluated);

        // // Get the vehicule to which the Document is related
        // $user = $userRepository->findOneByIdField($user_id);
        // $user->addDocument($newDocument);
        // $em->persist($newDocument);
        // $em->flush();
        
        // // Rename the Document in the database
        // $newDocument->setPath('/media/documents/' . $newDocument->getPath());
        // $em->persist($newDocument);
        // $em->flush();

        // return $this->json([
        //     'message' => 'Document successfully added',
        // ]);

    }

}

function associateState($string){
    switch ($string) {
        case 'UNEVALUATED':
            return State::Unevaluated;
        case 'VALID':
            return State::Valid;
        case 'INVALID':
            return State::Invalid;
    }
}

