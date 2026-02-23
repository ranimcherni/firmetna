<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_front_dashboard')]
    public function index(): Response
    {
        // Get current user or create default user data
        $user = $this->getUser();
        $prenom = $user ? $user->getPrenom() : 'Visiteur';
        
        return $this->render('front/dashboard.html.twig', [
            'user' => $user,
            'prenom' => $prenom
        ]);
    }
}
