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

}
