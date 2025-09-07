<?php

namespace App\Repository;

use App\Entity\Documents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documents>
 */
class DocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documents::class);
    }

    //    /**
    //     * @return Documents[] Returns an array of Documents objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Documents
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findOneByIdField($document_id): ?Documents
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.id = :document_id')
            ->setParameter('document_id', $document_id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findApplierDocumentsByApplicationId($application_id): array
    {
        $connection = $this->getEntityManager()->getConnection();
        
        $query = '
            SELECT DISTINCT d.path, md.state, rd.name, rd.informations 
            FROM required_documents rd
            JOIN match_documents md
            ON rd.id = md.required_document_id
            JOIN documents d
            ON md.document_id = d.id
            JOIN applications_documents ad
            ON d.id = ad.documents_id
            JOIN applications a
            ON ad.applications_id = a.id
            WHERE a.id = :application_id
        ';

        $result = $connection
        ->executeQuery($query, [
            'application_id' => $application_id,
        ]);

        return $result->fetchAllAssociative();
    }

    public function findDocumentsAssociatedToAppliedOfferByOfferAndUserId($offer_id, $user_id): array
    {
        $connection = $this->getEntityManager()->getConnection();
        
        $query = '
            SELECT DISTINCT d.path, md.state, rd.name, rd.informations 
            FROM users u
            JOIN documents d
            ON u.id = d.user_id
            JOIN match_documents md
            ON d.id = md.document_id
            JOIN required_documents rd
            ON md.required_document_id = rd.id
            JOIN offers o
            ON rd.offer_id = o.id
            WHERE o.id = :offer_id
            AND d.user_id = :user_id
        ';

        $result = $connection
        ->executeQuery($query, [
            'offer_id' => $offer_id,
            'user_id' => $user_id,
        ]);

        return $result->fetchAllAssociative();
    }
}
