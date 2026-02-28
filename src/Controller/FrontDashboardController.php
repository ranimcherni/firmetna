<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_front_dashboard')]
    public function index(
        \App\Repository\CommandeRepository $commandeRepository,
        \App\Repository\DemandeRepository $demandeRepository,
        \App\Repository\PublicationRepository $publicationRepository
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Recent orders
        $recentOrders = $commandeRepository->findBy(['client' => $user], ['dateCommande' => 'DESC'], 3);
        
        // Recent donation requests
        $recentDemandes = $demandeRepository->findBy(['demandeur' => $user], ['createdAt' => 'DESC'], 3);

        // Trending Forum
        $trendingForum = $publicationRepository->findMostLiked(3);

        return $this->render('front/dashboard.html.twig', [
            'recentOrders' => $recentOrders,
            'recentDemandes' => $recentDemandes,
            'trendingForum' => $trendingForum,
            'totalOrders' => $commandeRepository->count(['client' => $user]),
            'totalDemandes' => $demandeRepository->count(['demandeur' => $user]),
        ]);
    }
}
