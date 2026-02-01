<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PlaceholderController extends AbstractController
{
    #[Route('/produits', name: 'app_admin_produits')]
    #[Route('/produit', name: 'app_admin_produit')]
    public function produits(): Response
    {
        return $this->render('admin/placeholder.html.twig', [
            'module' => 'Produits',
            'icon' => 'fas fa-box'
        ]);
    }

    #[Route('/evenements', name: 'app_admin_evenements')]
    #[Route('/evenement', name: 'app_admin_evenement')]
    public function evenements(): Response
    {
        return $this->render('admin/placeholder.html.twig', [
            'module' => 'Événements',
            'icon' => 'fas fa-calendar-alt'
        ]);
    }

    #[Route('/forum', name: 'app_admin_forum')]
    public function forum(): Response
    {
        return $this->render('admin/placeholder.html.twig', [
            'module' => 'Forum',
            'icon' => 'fas fa-comments'
        ]);
    }

    #[Route('/donations', name: 'app_admin_donations')]
    #[Route('/donation', name: 'app_admin_donation')]
    public function donations(): Response
    {
        return $this->render('admin/placeholder.html.twig', [
            'module' => 'Donations',
            'icon' => 'fas fa-heart'
        ]);
    }

    #[Route('/commandes', name: 'app_admin_commandes')]
    #[Route('/commande', name: 'app_admin_commande')]
    public function commandes(): Response
    {
        return $this->render('admin/placeholder.html.twig', [
            'module' => 'Commandes',
            'icon' => 'fas fa-shopping-cart'
        ]);
    }
}
