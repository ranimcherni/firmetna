<?php

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publication>
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    /** @return Publication[] */
    public function findAllOrderByDateDesc(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithCommentaires(int $id): ?Publication
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.commentaires', 'c')
            ->addSelect('c')
            ->leftJoin('c.auteur', 'ca')
            ->addSelect('ca')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
