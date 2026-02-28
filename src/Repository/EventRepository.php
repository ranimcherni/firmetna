<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[]
     */
    public function findAllOrderByDate(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events in a date range. Both parameters are optional.
     *
     * @return Event[]
     */
    public function findByDateRange(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.date', 'ASC');

        if ($from !== null) {
            $qb->andWhere('e.date >= :from')->setParameter('from', $from);
        }

        if ($to !== null) {
            $qb->andWhere('e.date <= :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }
}
