<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produits')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'app_products')]
    public function index(): Response
    {
        return $this->render('front/products/categories.html.twig');
    }

    #[Route('/vegetale', name: 'app_products_vegetale')]
    public function vegetale(): Response
    {
        return $this->render('front/products/marketplace.html.twig', [
            'category' => 'Produits Végétaux',
            'type' => 'vegetale'
        ]);
    }

    #[Route('/animale', name: 'app_products_animale')]
    public function animale(): Response
    {
        return $this->render('front/products/marketplace.html.twig', [
            'category' => 'Produits Animaux',
            'type' => 'animale'
        ]);
    }
}
