<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user, bool $flush = true): void
    {
        $this->getEntityManager()->persist($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array Returns an array of most active users (based on number of publications)
    */
    public function findTopContributors(int $limit = 5): array
    {
        return $this->createQueryBuilder('u')
            ->select('u as user', 'COUNT(p.id) as publicationsCount')
            ->leftJoin('App\Entity\Publication', 'p', 'WITH', 'p.auteur = u')
            ->groupBy('u.id')
            ->having('publicationsCount > 0')
            ->orderBy('publicationsCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
