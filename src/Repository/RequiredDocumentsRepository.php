<?php

namespace App\Repository;

use App\Entity\RequiredDocuments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequiredDocuments>
 */
class RequiredDocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequiredDocuments::class);
    }

    //    /**
    //     * @return RequiredDocuments[] Returns an array of RequiredDocuments objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RequiredDocuments
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findOneByIdField(int | string $required_document_id): ?RequiredDocuments
    {
        return $this->createQueryBuilder('rd')
            ->andWhere('rd.id = :required_document_id')
            ->setParameter('required_document_id', $required_document_id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
