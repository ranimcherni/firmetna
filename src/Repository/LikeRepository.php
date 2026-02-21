<?php

namespace App\Repository;

use App\Entity\Like;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function findLikeByUserAndPublication(User $user, Publication $publication): ?Like
    {
        return $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->andWhere('l.publication = :publication')
            ->setParameter('user', $user)
            ->setParameter('publication', $publication)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countLikesByPublication(Publication $publication): int
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.publication = :publication')
            ->setParameter('publication', $publication)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
