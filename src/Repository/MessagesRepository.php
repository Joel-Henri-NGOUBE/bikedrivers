<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Messages>
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    //    /**
    //     * @return Messages[] Returns an array of Messages objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findByUserFields($offer_id, $user_id): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.offer = :offer')
            ->andWhere('m.user_recipient = :user')
            ->orWhere('m.user_sender = :user')
            ->setParameter('offer', $offer_id)
            ->setParameter('user', $user_id)
            // ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function setContentById($message_id, $content): array
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.content', ':content')
            // ->set('m.createdAt', new \DateTimeImmutable())
            ->andWhere('m.id = :message')
            ->setParameter('content', $content)
            ->setParameter('message', $message_id)
            ->getQuery()
            ->execute();
            ;

        return $this->createQueryBuilder('m')    
            ->where('m.id = :message')
            ->setParameter('message', $message_id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUsersByIdAndOfferIdFields($offer_id, $publisher_id): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = '
            SELECT CONCAT(firstname, " ", lastname) users FROM users 
            WHERE id IN (
                SELECT user_recipient_id FROM messages
                WHERE user_recipient_id <> :user_id
                AND offer_id = :offer_id
                )
            OR id IN (
                SELECT user_sender_id FROM messages
                WHERE user_sender_id <> :user_id
                AND offer_id = :offer_id
                )
            ;
            ';

        $result = $connection
        ->executeQuery($query, [
            'user_id' => $publisher_id,
            'offer_id' => $offer_id,
        ]);

        return $result->fetchAllAssociative();
    }

    public function deleteByIdField($message_id): void
    {
        $this->createQueryBuilder('m')
        ->delete()
        ->where('m.id = :message_id')
        ->setParameter(':message_id', $message_id)
        ->getQuery()
        ->execute()
        ;
    }

    //    public function findOneBySomeField($value): ?Messages
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
