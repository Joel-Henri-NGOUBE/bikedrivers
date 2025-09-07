<?php

namespace App\Controller;

use App\Entity\Pictures;
use App\Repository\PicturesRepository;
use App\Repository\VehiclesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PicturesController extends AbstractController
{
    public function __invoke($user_id, $vehicle_id, Pictures $pictures, Request $request, PicturesRepository $picturesRepository, VehiclesRepository $vehiclesRepository, EntityManagerInterface $em): JsonResponse
    {
        // Retrieve the picture transfered in the form
        $file = $request->files->get('file');
        $newPicture = new Pictures();
        $newPicture->setPictureFile($file);

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

    }
}
