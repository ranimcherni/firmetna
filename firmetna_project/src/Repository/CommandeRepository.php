<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * @return Commande[]
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'u')->addSelect('u')
            ->leftJoin('c.lignes', 'l')->addSelect('l')
            ->leftJoin('l.produit', 'p')->addSelect('p')
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Commande[]
     */
    public function findByClient(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.client = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByStatut(string $statut): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
