<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/donations')]
class DonationsController extends AbstractController
{
    #[Route('/', name: 'app_donations')]
    public function index(): Response
    {
        return $this->render('front/placeholder.html.twig', [
            'module' => 'Donations & Aide',
            'icon' => 'fas fa-hand-holding-heart',
            'description' => 'Soutenez nos agriculteurs locaux par vos dons et actions solidaires.'
        ]);
    }
}
