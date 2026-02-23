<?php

namespace App\Repository;

use App\Entity\Demande;
use App\Entity\Offre;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demande>
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

    /** @return Demande[] */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.offre', 'o')
            ->leftJoin('d.demandeur', 'u')
            ->addSelect('o', 'u')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function userADejaDemande(User $user, Offre $offre): bool
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->andWhere('d.offre = :offre')
            ->andWhere('d.demandeur = :user')
            ->setParameter('offre', $offre)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function save(Demande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
