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
        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $userRepository->count([]),
            'totalProducts' => 12, // Placeholder
            'totalEvents' => 5,    // Placeholder
            'totalDonations' => '2.5k' // Placeholder
        ]);
    }
}
