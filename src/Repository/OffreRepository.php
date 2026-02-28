<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offre>
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    /** @return Offre[] */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Offre[] */
    public function findDisponibles(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.disponible = :dispo')
            ->setParameter('dispo', true)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Offre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findBySearchAndSort(?string $search, ?string $sort): array
{
    $qb = $this->createQueryBuilder('o');

    // Recherche par catégorie ou description
    if ($search) {
        $qb->andWhere('o.categorie LIKE :search OR o.description LIKE :search')
           ->setParameter('search', '%'.$search.'%');
    }

        // Tri
        switch ($sort) {
            case 'recent':
                $qb->orderBy('o.createdAt', 'DESC');
                break;
            case 'oldest':
                $qb->orderBy('o.createdAt', 'ASC');
                break;
            case 'qty_desc':
                $qb->orderBy('o.quantite', 'DESC');
                break;
            case 'qty_asc':
                $qb->orderBy('o.quantite', 'ASC');
                break;
            case 'categorie':
                $qb->orderBy('o.categorie', 'ASC');
                break;
            default:
                $qb->orderBy('o.createdAt', 'DESC');
        }

    return $qb->getQuery()->getResult();
}
// src/Repository/OffreRepository.php

public function findTopDonations(int $limit = 3): array
{
    return $this->createQueryBuilder('o')
        ->orderBy('o.quantite', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

public function getTotalDonationsCount(): int
{
    return $this->createQueryBuilder('o')
        ->select('count(o.id)')
        ->getQuery()
        ->getSingleScalarResult();
}

// src/Repository/OffreRepository.php

/**
 * 1️⃣ Top donateurs par fréquence (Nombre d'offres)
 */
public function findTopDonorsByFrequency(int $limit = 5): array
{
    return $this->createQueryBuilder('o')
        ->select('o.telephone, COUNT(o.id) as nombreOffres, SUM(o.quantite) as totalQuantite')
        ->groupBy('o.telephone')
        ->orderBy('nombreOffres', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

/**
 * 2️⃣ Top donateurs par générosité (Quantité totale)
 */
public function findTopDonorsByQuantity(int $limit = 5): array
{
    return $this->createQueryBuilder('o')
        ->select('o.telephone, COUNT(o.id) as nombreOffres, SUM(o.quantite) as totalQuantite')
        ->groupBy('o.telephone')
        ->orderBy('totalQuantite', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

/**
 * 3️⃣ Répartition par catégorie
 */
public function getStatsByCategory(): array
{
    return $this->createQueryBuilder('o')
        ->select('o.categorie, COUNT(o.id) as count')
        ->groupBy('o.categorie')
        ->getQuery()
        ->getResult();
}
}
