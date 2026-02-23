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

    public function getMonthlyStatistics(int $months = 12): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as commandCount, SUM(c.total) as totalRevenue, SUBSTRING(c.dateCommande, 1, 7) as month')
            ->where('c.dateCommande >= :startDate')
            ->setParameter('startDate', new \DateTime("-{$months} months"))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();

        $monthlyData = [];
        $currentDate = new \DateTime();
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = (new \DateTime())->modify("-{$i} months");
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');
            
            $monthlyData[$monthKey] = [
                'month' => $monthLabel,
                'commandCount' => 0,
                'totalRevenue' => 0,
            ];
        }

        foreach ($qb as $row) {
            $monthlyData[$row['month']] = [
                'month' => $monthlyData[$row['month']]['month'],
                'commandCount' => (int) $row['commandCount'],
                'totalRevenue' => (float) $row['totalRevenue'],
            ];
        }

        return array_values($monthlyData);
    }

    public function getCommandStats(): array
    {
        $totalCommands = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $totalRevenue = $this->createQueryBuilder('c')
            ->select('SUM(c.total)')
            ->getQuery()
            ->getSingleScalarResult();

        $avgRevenue = $this->createQueryBuilder('c')
            ->select('AVG(c.total)')
            ->getQuery()
            ->getSingleScalarResult();

        $statsByStatus = [];
        $statuses = [Commande::STATUT_EN_ATTENTE, Commande::STATUT_CONFIRMEE, Commande::STATUT_EXPEDIEE, Commande::STATUT_LIVREE, Commande::STATUT_ANNULEE];
        foreach ($statuses as $status) {
            $statsByStatus[$status] = $this->countByStatut($status);
        }

        return [
            'totalCommands' => (int) $totalCommands,
            'totalRevenue' => (float) $totalRevenue,
            'avgRevenue' => round((float) $avgRevenue, 2),
            'statsByStatus' => $statsByStatus,
        ];
    }
}
