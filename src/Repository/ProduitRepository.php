<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;

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
     * Get paginated products by type
     */
    public function findByTypePaginated(string $type, int $page = 1, int $limit = 6): array
    {
        $offset = ($page - 1) * $limit;
        
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->setParameter('type', $type)
            ->orderBy('p.nom', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        
        return [
            'products' => $paginator->getIterator()->getArrayCopy(),
            'total' => count($paginator),
            'pages' => ceil(count($paginator) / $limit),
            'current_page' => $page,
            'has_next' => $page < ceil(count($paginator) / $limit),
            'has_previous' => $page > 1
        ];
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

    public function getStatistics(): array
    {
        $qb = $this->createQueryBuilder('p');
        
        $totalProducts = $qb->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $vegetableCount = $this->countByType('vegetale');
        $animalCount = $this->countByType('animale');

        $stockStats = $this->createQueryBuilder('p')
            ->select('SUM(p.stock) as totalStock, AVG(p.stock) as avgStock')
            ->getQuery()
            ->getSingleResult();

        $bioCount = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.isBio = true')
            ->getQuery()
            ->getSingleScalarResult();

        $outOfStockCount = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.stock <= 0')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'totalProducts' => (int) $totalProducts,
            'vegetableCount' => (int) $vegetableCount,
            'animalCount' => (int) $animalCount,
            'totalStock' => (int) $stockStats['totalStock'],
            'avgStock' => round((float) $stockStats['avgStock'], 2),
            'bioCount' => (int) $bioCount,
            'outOfStockCount' => (int) $outOfStockCount,
        ];
    }
}
