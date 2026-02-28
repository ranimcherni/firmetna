<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @return Produit[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->setParameter('type', $type)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Produit[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.type', 'ASC')
            ->addOrderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countByType(string $type): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getGlobalStats(): array
    {
        $qb = $this->createQueryBuilder('p');
        
        $totalCount = (int) $qb->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
        
        $vegetalCount = (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.type = :type')
            ->setParameter('type', 'vegetale')
            ->getQuery()
            ->getSingleScalarResult();
            
        $animaleCount = (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.type = :type')
            ->setParameter('type', 'animale')
            ->getQuery()
            ->getSingleScalarResult();

        $bioCount = (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.isBio = :isBio')
            ->setParameter('isBio', true)
            ->getQuery()
            ->getSingleScalarResult();

        $totalValue = (float) $this->createQueryBuilder('p')
            ->select('SUM(p.prix * p.stock)')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $totalCount,
            'vegetal' => $vegetalCount,
            'animale' => $animaleCount,
            'bio' => $bioCount,
            'totalValue' => $totalValue,
            'distribution' => [
                'Vegetal' => $totalCount > 0 ? round(($vegetalCount / $totalCount) * 100) : 0,
                'Animal' => $totalCount > 0 ? round(($animaleCount / $totalCount) * 100) : 0,
            ]
        ];
    }
}
