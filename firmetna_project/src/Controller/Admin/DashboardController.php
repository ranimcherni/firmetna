<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(\App\Repository\UserRepository $userRepository): Response
    {
        // Real user stats
        $usersCount = $userRepository->count([]);
        $agriculteurs = $userRepository->count(['roleType' => 'Agriculteur']);
        $clients = $userRepository->count(['roleType' => 'Client']);
        $donateurs = $userRepository->count(['roleType' => 'Donateur']);

        // Mock data for graphs (since other entities aren't fully ready yet)
        $monthlySales = [1200, 1500, 1100, 1800, 2200, 2500, 2100]; // 7 last months
        $productDistribution = [
            'Vegetal' => 65,
            'Animal' => 35
        ];

        // Top Products with visual info
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
            ],
            [
                'name' => 'Tomates Séchées au Soleil',
                'category' => 'Végétal',
                'sales' => 98,
                'image' => 'https://images.unsplash.com/photo-1590779033100-9f60705a2f3b?w=400&h=400&fit=crop',
                'growth' => '+8%'
            ],
            [
                'name' => 'Myrtilles (Blueberries)',
                'category' => 'Végétal',
                'sales' => 85,
                'image' => 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=400&h=400&fit=crop',
                'growth' => '+22%'
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
            'totalDonations' => '2,540'
        ]);
    }
}
