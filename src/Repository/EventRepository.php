<?php

namespace App\Repository;

use App\Entity\Event;
use DateTimeInterface;
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
     * @return Event[]
     */
    public function findByDateRange(?DateTimeInterface $from, ?DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($from) {
            $qb->andWhere('e.date >= :from')
               ->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('e.date <= :to')
               ->setParameter('to', $to);
        }

        return $qb
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
