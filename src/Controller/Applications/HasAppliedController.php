<?php

namespace App\Controller\Applications;

use App\Repository\ApplicationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class HasAppliedController extends AbstractController
{
    public function __invoke(int | string $offer_id, int | string $user_id, Request $request, ApplicationsRepository $applicationsRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $applicationsRepository->findIfUserHasApplied($offer_id, $user_id);

        if (count($user)) {
            return $this->json([
                'hasApplied' => true,
            ]);
        }

        return $this->json([
            'hasApplied' => false,
        ]);

    }
}
