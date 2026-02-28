<?php

namespace App\Controller\Admin;

use App\Entity\Contract;
use App\Repository\ContractRepository;
use App\Service\PartnerMatchingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartnerMatchingController extends AbstractController
{
    #[Route('/admin/partner-matching', name: 'app_admin_partner_matching')]
    public function index(ContractRepository $contractRepository, PartnerMatchingService $matchingService): Response
    {
        $contracts = $contractRepository->findAll();
        $statistics = $matchingService->getMatchingStatistics();
        
        // Get matches for each contract
        $contractsWithMatches = [];
        foreach ($contracts as $contract) {
            $matches = $matchingService->findBestMatches($contract, 3);
            $contractsWithMatches[] = [
                'contract' => $contract,
                'matches' => $matches
            ];
        }

        return $this->render('admin/partner_matching/index.html.twig', [
            'contractsWithMatches' => $contractsWithMatches,
            'statistics' => $statistics
        ]);
    }

    #[Route('/admin/partner-matching/api/contract/{id}/matches', name: 'app_admin_partner_matching_contract_api', methods: ['GET'])]
    public function getContractMatchesApi(Contract $contract, PartnerMatchingService $matchingService): JsonResponse
    {
        $matches = $matchingService->findBestMatches($contract, 5);
        
        $data = [];
        foreach ($matches as $match) {
            $data[] = [
                'partner_id' => $match['partner']->getId(),
                'partner_name' => $match['partner']->getName(),
                'partner_type' => $match['partner']->getType(),
                'partner_email' => $match['partner']->getEmail(),
                'partner_phone' => $match['partner']->getPhone(),
                'score' => round($match['score'], 1),
                'reasons' => $match['reasons'],
                'contracts_count' => $match['partner']->getOffers()->count()
            ];
        }
        
        return new JsonResponse($data);
    }

    #[Route('/admin/partner-matching/api/statistics', name: 'app_admin_partner_matching_statistics_api', methods: ['GET'])]
    public function getStatisticsApi(PartnerMatchingService $matchingService): JsonResponse
    {
        $statistics = $matchingService->getMatchingStatistics();
        
        // Format for API response
        $data = [
            'total_partners' => $statistics['total_partners'],
            'total_contracts' => $statistics['total_contracts'],
            'avg_compatibility_score' => $statistics['avg_compatibility_score'],
            'top_performers' => array_map(function($item) {
                return [
                    'partner_name' => $item['partner']->getName(),
                    'avg_score' => round($item['total_score'] / $item['count'], 1),
                    'match_count' => $item['count']
                ];
            }, $statistics['top_performing_partners'])
        ];
        
        return new JsonResponse($data);
    }
}
