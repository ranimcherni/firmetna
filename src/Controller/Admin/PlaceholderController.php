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

    #[Route('/partenariats', name: 'app_admin_partenariats')]
    #[Route('/partenariat', name: 'app_admin_partenariat')]
    public function partenariats(): Response
    {
        return $this->render('admin/placeholder.html.twig', [
            'module' => 'Partenariats',
            'icon' => 'fas fa-handshake'
        ]);
    }
}
