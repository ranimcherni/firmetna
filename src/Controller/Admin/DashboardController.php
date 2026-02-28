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
        \App\Repository\CommandeRepository $commandeRepository,
        \App\Repository\PartnerRepository $partnerRepository,
        \App\Repository\ContractRepository $contractRepository
    ): Response
    {
        // Real counts
        $usersCount = $userRepository->count([]);
        $agriculteurs = $userRepository->count(['roleType' => 'Agriculteur']);
        $clients = $userRepository->count(['roleType' => 'Client']);
        $donateurs = $userRepository->count(['roleType' => 'Donateur']);

        $stats = $produitRepository->getGlobalStats();
        $produitsCount = $stats['total'];
        $eventsCount = $eventRepository->count([]);
        $publicationsCount = $publicationRepository->count([]);
        $offresCount = $offreRepository->count([]);
        $commandesCount = $commandeRepository->count([]);
        $partnersCount = $partnerRepository->count([]);
        $contractsCount = $contractRepository->count([]);

        // Monthly sales mock for now (needs Order/Payment integration)
        $monthlySales = [1200, 1500, 1100, 1800, 2200, 2500, 2100];
        $productDistribution = $stats['distribution'];

        // Top Products from DB (Real)
        $topProductsEntities = $produitRepository->findBy([], ['stock' => 'DESC'], 3);
        $topProducts = [];
        foreach ($topProductsEntities as $p) {
            $topProducts[] = [
                'name' => $p->getNom(),
                'category' => ucfirst($p->getType()),
                'sales' => $p->getStock() > 0 ? rand(10, 100) : 0, // Mock sales count based on stock activity
                'image' => $p->getImageUrl() ?? 'https://images.unsplash.com/photo-1590779033100-9f60705a2f3b?w=400&h=400&fit=crop',
                'growth' => '+' . rand(5, 15) . '%'
            ];
        }

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
            'partnersCount' => $partnersCount,
            'contractsCount' => $contractsCount,
            'monthlySales' => $monthlySales,
            'productDistribution' => $productDistribution,
            'topProducts' => $topProducts,
            'stats' => $stats
        ]);
    }
}
