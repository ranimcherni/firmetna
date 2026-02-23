<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\PartnerRepository;
use App\Repository\ContractRepository;

class PublicAdminController extends AbstractController
{
    #[Route('/public-admin', name: 'app_public_admin')]
    public function dashboard(UserRepository $userRepository, PartnerRepository $partnerRepository, ContractRepository $offerRepository): Response
    {
        // Get real stats for dashboard
        $usersCount = $userRepository->count([]);
        $agriculteurs = $userRepository->count(['roleType' => 'Agriculteur']);
        $clients = $userRepository->count(['roleType' => 'Client']);
        $donateurs = $userRepository->count(['roleType' => 'Donateur']);

        // Get partners and offers
        $partners = $partnerRepository->findAll();
        $offers = $offerRepository->findAll();

        // Mock data for graphs
        $monthlySales = [1200, 1500, 1100, 1800, 2200, 2500, 2100];
        $productDistribution = ['Vegetal' => 65, 'Animal' => 35];

        $topProducts = [
            [
                'name' => 'Fromage Artisanal',
                'category' => 'Animal',
                'sales' => 145,
                'image' => 'https://images.unsplash.com/photo-1486297678162-ad2a19b05840?w=400&h=400&fit=crop',
                'growth' => '+12%'
            ],
            [
                'name' => 'Miel Bio de Montagne',
                'category' => 'Animal',
                'sales' => 112,
                'image' => 'https://images.unsplash.com/photo-1589927986089-35812388d1f4?w=400&h=400&fit=crop',
                'growth' => '+15%'
            ]
        ];

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $usersCount,
            'agriculteursCount' => $agriculteurs,
            'clientsCount' => $clients,
            'donateursCount' => $donateurs,
            'monthlySales' => $monthlySales,
            'productDistribution' => $productDistribution,
            'topProducts' => $topProducts,
            'totalDonations' => '2,540',
            'partners' => $partners,
            'offers' => $offers
        ]);
    }

    #[Route('/public-admin/partenariats', name: 'app_public_admin_partners')]
    public function partners(PartnerRepository $partnerRepository): Response
    {
        $partners = $partnerRepository->findAll();
        return $this->render('admin/partner/index.html.twig', [
            'partners' => $partners
        ]);
    }

    #[Route('/public-admin/partenariats/offres', name: 'app_public_admin_offers')]
    public function partnerOffers(ContractRepository $offerRepository): Response
    {
        $offers = $offerRepository->findAll();
        return $this->render('admin/partner_offer/index.html.twig', [
            'partner_offers' => $offers
        ]);
    }
}
