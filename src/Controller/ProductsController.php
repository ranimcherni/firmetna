<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Form\CommandeOrderType;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/produits')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'app_products')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $countVegetale = $produitRepository->countByType('vegetale');
        $countAnimale = $produitRepository->countByType('animale');
        return $this->render('front/products/categories.html.twig', [
            'countVegetale' => $countVegetale,
            'countAnimale' => $countAnimale,
        ]);
    }

    #[Route('/vegetale', name: 'app_products_vegetale')]
    public function vegetale(ProduitRepository $produitRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $produitRepository->findByTypePaginated('vegetale', $page, 6);
        
        return $this->render('front/products/marketplace.html.twig', [
            'category' => 'Produits Végétaux',
            'type' => 'vegetale',
            'pagination' => $pagination
        ]);
    }

    #[Route('/animale', name: 'app_products_animale')]
    public function animale(ProduitRepository $produitRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $produitRepository->findByTypePaginated('animale', $page, 6);
        
        return $this->render('front/products/marketplace.html.twig', [
            'category' => 'Produits Animaux',
            'type' => 'animale',
            'pagination' => $pagination
        ]);
    }


    #[Route('/{id}/commander', name: 'app_products_order', requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function order(Produit $produit, Request $request, CartService $cartService): Response
    {
        $type = $produit->getType();
        $unite_labels = [
            'kilo' => 'Par kilo',
            'unite' => "À l'unité",
            'boite' => 'Boîte',
            'barquette' => 'Barquette'
        ];

        // We use a dummy Commande object just to back the form
        $commande = new Commande();
        $commande->setClient($this->getUser());
        $form = $this->createForm(CommandeOrderType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantite = $form->get('quantite')->getData();

            $cartService->add($produit->getId(), [
                'quantity' => $quantite,
                'address' => $commande->getAdresseLivraison(),
                'comment' => $commande->getCommentaire()
            ]);

            $this->addFlash('success', 'Produit ajouté au panier !');
            
            // Redirect back to the list instead of the cart
            return $this->redirectToRoute($type === 'vegetale' ? 'app_products_vegetale' : 'app_products_animale');
        }

        return $this->render('front/products/order.html.twig', [
            'form' => $form,
            'produit' => $produit,
            'type' => $type,
            'unite_labels' => $unite_labels,
        ]);
    }

    #[Route('/dashboard', name: 'app_products_dashboard')]
    public function dashboard(ProduitRepository $produitRepository, CommandeRepository $commandeRepository): Response
    {
        $productStats = $produitRepository->getStatistics();
        $commandStats = $commandeRepository->getCommandStats();
        $monthlyStats = $commandeRepository->getMonthlyStatistics(12);

        return $this->render('front/products/dashboard.html.twig', [
            'productStats' => $productStats,
            'commandStats' => $commandStats,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    #[Route('/vendre/nouveau', name: 'app_products_sell', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function sell(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if the user is an agriculteur
        $user = $this->getUser();
        if ($user->getRoleType() !== 'Agriculteur') {
            $this->addFlash('error', 'Seul un agriculteur peut vendre un produit');
            return $this->redirectToRoute('app_products');
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Produit créé avec succès!');
            return $this->redirectToRoute('app_products');
        }

        return $this->render('front/products/sell.html.twig', [
            'form' => $form,
        ]);
    }
}

