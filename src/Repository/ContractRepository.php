<?php

namespace App\Repository;

use App\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contract>
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    /**
     * @return Contract[]
     */
    public function findByPartnerOrderByDate(int $partnerId): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.partner', 'p')
            ->andWhere('p.id = :partnerId')
            ->setParameter('partnerId', $partnerId)
            ->orderBy('o.offerDate', 'DESC')
            ->addOrderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Contract[]
     */
    public function findAllOrderByDate(): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.partner', 'p')
            ->addSelect('p')
            ->orderBy('o.offerDate', 'DESC')
            ->addOrderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
