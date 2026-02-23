<?php

namespace App\Service;

use App\Entity\Contract;
use App\Entity\Partner;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PartnerMatchingService
{
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $doctrine;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
    }

    public function findBestMatches(Contract $contract, int $limit = 5): array
    {
        $partners = $this->entityManager->getRepository(Partner::class)->findAll();
        $matches = [];

        foreach ($partners as $partner) {
            if ($partner->getId() === $contract->getPartner()?->getId()) {
                continue; // Skip current partner
            }

            $score = $this->calculateCompatibilityScore($contract, $partner);
            $matches[] = [
                'partner' => $partner,
                'score' => $score,
                'reasons' => $this->getMatchingReasons($contract, $partner, $score)
            ];
        }

        // Sort by score descending
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($matches, 0, $limit);
    }

    public function calculateCompatibilityScore(Contract $contract, Partner $partner): float
    {
        $score = 0;

        // Type compatibility (40% weight)
        $score += $this->getTypeCompatibilityScore($contract, $partner) * 0.4;

        // Historical performance (30% weight)
        $score += $this->getHistoricalScore($partner) * 0.3;

        // Amount compatibility (20% weight)
        $score += $this->getAmountCompatibilityScore($contract, $partner) * 0.2;

        // Recent activity (10% weight)
        $score += $this->getRecentActivityScore($partner) * 0.1;

        return min($score, 100); // Cap at 100
    }

    private function getTypeCompatibilityScore(Contract $contract, Partner $partner): float
    {
        $contractType = strtolower($contract->getType());
        $partnerType = strtolower($partner->getType());

        $compatibilityMatrix = [
            'sponsorship' => [
                'association' => 80,
                'entreprise' => 95,
                'institution' => 75,
                'particulier' => 50
            ],
            'product' => [
                'entreprise' => 90,
                'association' => 65,
                'institution' => 70,
                'particulier' => 55
            ],
            'service' => [
                'entreprise' => 85,
                'association' => 75,
                'institution' => 80,
                'particulier' => 60
            ],
            'other' => [
                'entreprise' => 60,
                'association' => 70,
                'institution' => 65,
                'particulier' => 75
            ]
        ];

        return $compatibilityMatrix[$contractType][$partnerType] ?? 50;
    }

    private function getHistoricalScore(Partner $partner): float
    {
        $contracts = $partner->getOffers();
        if ($contracts->count() === 0) {
            return 30; // New partner gets base score
        }

        $completedCount = 0;
        $totalCount = $contracts->count();

        foreach ($contracts as $contract) {
            if (strtolower($contract->getStatus()) === 'terminé' || strtolower($contract->getStatus()) === 'termine') {
                $completedCount++;
            }
        }

        $completionRate = ($completedCount / $totalCount) * 100;
        
        // Bonus for experience
        $experienceBonus = min($totalCount * 2, 20); // Max 20 points for experience
        
        return min($completionRate + $experienceBonus, 100);
    }

    private function getAmountCompatibilityScore(Contract $contract, Partner $partner): float
    {
        if (!$contract->getAmount()) {
            return 70; // Neutral score if no amount
        }

        $contracts = $partner->getOffers();
        if ($contracts->count() === 0) {
            return 60; // New partner
        }

        $amounts = [];
        foreach ($contracts as $c) {
            if ($c->getAmount()) {
                $amounts[] = floatval($c->getAmount());
            }
        }

        if (empty($amounts)) {
            return 60;
        }

        $avgAmount = array_sum($amounts) / count($amounts);
        $currentAmount = floatval($contract->getAmount());

        // Calculate compatibility based on amount range
        $ratio = $currentAmount / $avgAmount;
        
        if ($ratio >= 0.8 && $ratio <= 1.2) {
            return 90; // Very compatible
        } elseif ($ratio >= 0.6 && $ratio <= 1.5) {
            return 75; // Compatible
        } elseif ($ratio >= 0.4 && $ratio <= 2.0) {
            return 60; // Somewhat compatible
        } else {
            return 40; // Not very compatible
        }
    }

    private function getRecentActivityScore(Partner $partner): float
    {
        $contracts = $partner->getOffers();
        if ($contracts->count() === 0) {
            return 20;
        }

        $latestDate = null;
        foreach ($contracts as $contract) {
            $contractDate = $contract->getCreatedAt() ?? $contract->getOfferDate();
            if ($contractDate && (!$latestDate || $contractDate > $latestDate)) {
                $latestDate = $contractDate;
            }
        }

        if (!$latestDate) {
            return 20;
        }

        $daysSinceActivity = (new \DateTime())->diff($latestDate)->days;
        
        if ($daysSinceActivity <= 30) {
            return 100; // Very active
        } elseif ($daysSinceActivity <= 90) {
            return 80; // Active
        } elseif ($daysSinceActivity <= 180) {
            return 60; // Moderately active
        } elseif ($daysSinceActivity <= 365) {
            return 40; // Low activity
        } else {
            return 20; // Inactive
        }
    }

    private function getMatchingReasons(Contract $contract, Partner $partner, float $score): array
    {
        $reasons = [];

        // Type compatibility reasons
        $typeScore = $this->getTypeCompatibilityScore($contract, $partner);
        if ($typeScore >= 85) {
            $reasons[] = "Type d'activité parfaitement compatible";
        } elseif ($typeScore >= 70) {
            $reasons[] = "Type d'activité compatible";
        }

        // Historical performance reasons
        $historicalScore = $this->getHistoricalScore($partner);
        if ($historicalScore >= 80) {
            $reasons[] = "Excellente performance historique";
        } elseif ($historicalScore >= 60) {
            $reasons[] = "Bonne performance historique";
        }

        // Amount compatibility reasons
        $amountScore = $this->getAmountCompatibilityScore($contract, $partner);
        if ($amountScore >= 85) {
            $reasons[] = "Montant parfaitement adapté";
        } elseif ($amountScore >= 70) {
            $reasons[] = "Montant compatible";
        }

        // Activity reasons
        $activityScore = $this->getRecentActivityScore($partner);
        if ($activityScore >= 80) {
            $reasons[] = "Partenaire très actif";
        } elseif ($activityScore >= 60) {
            $reasons[] = "Partenaire actif";
        }

        // Experience reasons
        $contracts = $partner->getOffers();
        if ($contracts->count() >= 5) {
            $reasons[] = "Partenaire expérimenté (" . $contracts->count() . " contrats)";
        } elseif ($contracts->count() >= 2) {
            $reasons[] = "Partenaire avec expérience";
        }

        return $reasons;
    }

    public function getMatchingStatistics(): array
    {
        $partners = $this->entityManager->getRepository(Partner::class)->findAll();
        $contracts = $this->entityManager->getRepository(Contract::class)->findAll();

        $stats = [
            'total_partners' => count($partners),
            'total_contracts' => count($contracts),
            'avg_compatibility_score' => 0,
            'top_performing_partners' => [],
            'most_compatible_types' => []
        ];

        // Calculate average compatibility score
        $totalScore = 0;
        $count = 0;
        $partnerScores = [];

        foreach ($contracts as $contract) {
            $matches = $this->findBestMatches($contract, 3);
            foreach ($matches as $match) {
                $totalScore += $match['score'];
                $count++;
                
                // Track partner performance
                $partnerId = $match['partner']->getId();
                if (!isset($partnerScores[$partnerId])) {
                    $partnerScores[$partnerId] = ['partner' => $match['partner'], 'total_score' => 0, 'count' => 0];
                }
                $partnerScores[$partnerId]['total_score'] += $match['score'];
                $partnerScores[$partnerId]['count']++;
            }
        }

        $stats['avg_compatibility_score'] = $count > 0 ? round($totalScore / $count, 1) : 0;

        // Get top performing partners
        usort($partnerScores, function($a, $b) {
            $avgA = $a['count'] > 0 ? $a['total_score'] / $a['count'] : 0;
            $avgB = $b['count'] > 0 ? $b['total_score'] / $b['count'] : 0;
            return $avgB <=> $avgA;
        });

        $stats['top_performing_partners'] = array_slice($partnerScores, 0, 5);

        return $stats;
    }
}
