<?php

namespace App\Controller\Applications;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ApplicationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Applications;

final class AppliersController extends AbstractController
{
     public function __invoke($offer_id, Request $request, ApplicationsRepository $applicationsRepository, EntityManagerInterface $em): JsonResponse
    {
        $appliers = $applicationsRepository->findAppliersByOfferId($offer_id);

        return $this->json($appliers);

    }

}
