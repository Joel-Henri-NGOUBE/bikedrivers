<?php

namespace App\Repository;

use App\Entity\Applications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Applications>
 */
class ApplicationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Applications::class);
    }

    //    /**
    //     * @return Applications[] Returns an array of Applications objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Applications
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @return array<mixed>
     */
    public function findIfUserHasApplied(int | string $offer_id, int | string $user_id): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = '
            SELECT DISTINCT u.firstname, u.lastname
            FROM users u
            JOIN documents d
            ON u.id = d.user_id
            JOIN applications_documents ad
            ON d.id = ad.documents_id
            JOIN applications a
            ON ad.applications_id = a.id
            JOIN offers o
            ON a.offer_id = o.id
            WHERE o.id = :offer_id
            AND u.id = :user_id
        ';

        $result = $connection
            ->executeQuery($query, [
                'offer_id' => $offer_id,
                'user_id' => $user_id,
            ]);

        return $result->fetchAllAssociative();
    }

    /**
     * @return array<mixed>
     */
    public function findAppliersByOfferId(int | string $offer_id): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = '
            SELECT DISTINCT u.id user_id, u.firstname, u.lastname, a.state, a.id application_id, a.created_at application_date
            FROM users u
            JOIN documents d
            ON u.id = d.user_id
            JOIN applications_documents ad
            ON d.id = ad.documents_id
            JOIN applications a
            ON ad.applications_id = a.id
            JOIN offers o
            ON a.offer_id = o.id
            WHERE o.id = :offer_id
        ';

        $result = $connection
            ->executeQuery($query, [
                'offer_id' => $offer_id,
            ]);

        return $result->fetchAllAssociative();
    }

    public function findOneById(int | string $application_id): ?Applications
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :application_id')
            ->setParameter('application_id', $application_id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
