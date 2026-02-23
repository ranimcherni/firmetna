<?php

namespace App\Repository;

use App\Entity\EmailVerification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailVerification>
 */
class EmailVerificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerification::class);
    }

    public function save(EmailVerification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findValidByEmail(string $email): ?EmailVerification
    {
        return $this->createQueryBuilder('ev')
            ->where('ev.email = :email')
            ->andWhere('ev.isVerified = :isVerified')
            ->andWhere('ev.expiresAt > :expiresAt')
            ->setParameter('email', $email)
            ->setParameter('isVerified', false)
            ->setParameter('expiresAt', new \DateTimeImmutable())
            ->orderBy('ev.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function cleanupExpired(): void
    {
        $this->createQueryBuilder('ev')
            ->delete()
            ->where('ev.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
