<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        \App\Repository\UserRepository $userRepository,
        \App\Repository\ProduitRepository $produitRepository,
        \App\Repository\EventRepository $eventRepository,
        \App\Repository\PublicationRepository $publicationRepository,
        \App\Repository\OffreRepository $offreRepository,
        \App\Repository\CommandeRepository $commandeRepository
    ): Response
    {
        // Real counts
        $usersCount = $userRepository->count([]);
        $agriculteurs = $userRepository->count(['roleType' => 'Agriculteur']);
        $clients = $userRepository->count(['roleType' => 'Client']);
        $donateurs = $userRepository->count(['roleType' => 'Donateur']);

        $produitsCount = $produitRepository->count([]);
        $eventsCount = $eventRepository->count([]);
        $publicationsCount = $publicationRepository->count([]);
        $offresCount = $offreRepository->count([]);
        $commandesCount = $commandeRepository->count([]);

        // Mock data for graphs
        $monthlySales = [1200, 1500, 1100, 1800, 2200, 2500, 2100];
        $productDistribution = [
            'Vegetal' => 65,
            'Animal' => 35
        ];

        // Top Products Mock (Keep for visual)
        $topProducts = [
            [
                'name' => 'Fromage Artisanal',
                'category' => 'Animal',
                'sales' => 145,
                'image' => 'https://images.unsplash.com/photo-1486297678162-ad2a19b05840?w=400&h=400&fit=crop',
                'growth' => '+12%'
            ],
            [
                'name' => 'Tomates Séchées',
                'category' => 'Végétal',
                'sales' => 98,
                'image' => 'https://images.unsplash.com/photo-1590779033100-9f60705a2f3b?w=400&h=400&fit=crop',
                'growth' => '+8%'
            ]
        ];

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $usersCount,
            'agriculteursCount' => $agriculteurs,
            'clientsCount' => $clients,
            'donateursCount' => $donateurs,
            'produitsCount' => $produitsCount,
            'eventsCount' => $eventsCount,
            'publicationsCount' => $publicationsCount,
            'offresCount' => $offresCount,
            'commandesCount' => $commandesCount,
            'monthlySales' => $monthlySales,
            'productDistribution' => $productDistribution,
            'topProducts' => $topProducts
        ]);
    }
}
