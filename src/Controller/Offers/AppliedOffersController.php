<?php

namespace App\Controller\Offers;

use App\Repository\OffersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class AppliedOffersController extends AbstractController
{
    public function __invoke($user_id, Request $request, OffersRepository $offersRepository, EntityManagerInterface $em): JsonResponse
    {
        $appliedOffers = $offersRepository->findAppliedOffersByUserId($user_id);

        return $this->json($appliedOffers);

    }
}
