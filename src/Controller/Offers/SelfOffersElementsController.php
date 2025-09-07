<?php

namespace App\Controller\Offers;

use App\Repository\OffersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SelfOffersElementsController extends AbstractController
{
    public function __invoke($user_id, Request $request, OffersRepository $offersRepository, EntityManagerInterface $em): JsonResponse
    {
        $elements = $offersRepository->findOffersElementsByUserId($user_id);

        return $this->json($elements);

    }
}
