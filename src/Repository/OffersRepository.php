<?php

namespace App\Repository;

use App\Entity\Offers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offers>
 */
class OffersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offers::class);
    }

    //    /**
    //     * @return Offers[] Returns an array of Offers objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Offers
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findOneByIdField($id): ?Offers
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOffersElementsByUserId($user_id): array
    {
        $connection = $this->getEntityManager()->getConnection();
        
        // 'SELECT title, description FROM offers WHERE vehicle_id IN (SELECT id FROM vehicles WHERE user_id = :user_id)'
        $query = '
            SELECT o.title title, o.description description, v.model model, v.brand brand, o.id id_offer, o.status
            FROM vehicles v 
            JOIN offers o 
            ON v.id = o.vehicle_id
            AND v.user_id = :user_id
        ';

        $result = $connection
        ->executeQuery($query, [
            'user_id' => $user_id,
            // 'offer_id' => $offer_id,
        ]);

        return $result->fetchAllAssociative();
    }

    public function findAppliedOffersByUserId($user_id): array
    {
        $connection = $this->getEntityManager()->getConnection();
        
        // 'SELECT title, description FROM offers WHERE vehicle_id IN (SELECT id FROM vehicles WHERE user_id = :user_id)'
        $query = '
            SELECT DISTINCT o.title title, o.description description, v.model model, v.brand brand, o.id id_offer, o.status, a.created_at application_date 
            FROM users u
            JOIN documents d
            ON u.id = d.user_id
            JOIN applications_documents ad
            ON d.id = ad.documents_id
            JOIN applications a
            ON ad.applications_id = a.id
            JOIN offers o
            ON a.offer_id = o.id
            JOIN vehicles v
            ON o.vehicle_id = v.id
            AND d.user_id = :user_id
        ';

        $result = $connection
        ->executeQuery($query, [
            'user_id' => $user_id,
            // 'offer_id' => $offer_id,
        ]);

        return $result->fetchAllAssociative();
    }

    // public function findOneByIdField($id, $vehicle_id, $user_id): ?Vehicles
    // {
    //     return $this->createQueryBuilder('v')
    //         ->andWhere('v.id = :id')
    //         ->andWhere('v.user_id = :user')
    //         ->andWhere('v.vehicle = :vehicle')
    //         ->setParameter('id', $id)
    //         ->setParameter('user', $user_id)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
