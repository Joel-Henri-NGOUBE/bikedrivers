<?php

namespace App\Controller\Applications;

use App\Repository\ApplicationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class AppliersController extends AbstractController
{
    public function __invoke($offer_id, Request $request, ApplicationsRepository $applicationsRepository, EntityManagerInterface $em): JsonResponse
    {
        $appliers = $applicationsRepository->findAppliersByOfferId($offer_id);

        return $this->json($appliers);

    }
}
