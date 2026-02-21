<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Form\CommandeOrderType;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
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
    public function vegetale(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findByType('vegetale');
        return $this->render('front/products/marketplace.html.twig', [
            'category' => 'Produits Végétaux',
            'type' => 'vegetale',
            'produits' => $produits,
        ]);
    }

    #[Route('/animale', name: 'app_products_animale')]
    public function animale(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findByType('animale');
        return $this->render('front/products/marketplace.html.twig', [
            'category' => 'Produits Animaux',
            'type' => 'animale',
            'produits' => $produits,
        ]);
    }


    #[Route('/{id}/commander', name: 'app_products_order', requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function order(Produit $produit, Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $type = $produit->getType();
        $unite_labels = [
            'kilo' => 'Par kilo',
            'unite' => "À l'unité",
            'boite' => 'Boîte',
            'barquette' => 'Barquette'
        ];

        // Create a new Commande for the form
        $commande = new Commande();
        $commande->setClient($this->getUser());

        // Create the form
        $form = $this->createForm(CommandeOrderType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the quantity from the unmapped form field
            $quantite = $form->get('quantite')->getData();

            // Check stock availability
            if ($quantite > $produit->getStock()) {
                $this->addFlash('error', "Quantité insuffisante. Disponible: {$produit->getStock()} {$unite_labels[$produit->getUnite()]}");
                return $this->redirectToRoute('app_products_order', ['id' => $produit->getId()]);
            }

            // Create the line item (LigneCommande)
            $ligneCommande = new LigneCommande();
            $ligneCommande->setProduit($produit);
            $ligneCommande->setQuantite($quantite);
            $ligneCommande->setPrixUnitaire($produit->getPrix());
            $ligneCommande->setCommande($commande);

            // Add the line to the commande
            $commande->addLigne($ligneCommande);

            // Calculate total
            $commande->recalculerTotal();

            // Persist and flush
            $entityManager->persist($commande);
            $entityManager->persist($ligneCommande);
            $entityManager->flush();

            // Set success flash message
            $this->addFlash('success', 'Commande créée avec succès!');

            // Redirect to order details or dashboard
            return $this->redirectToRoute('app_front_dashboard');
        }

        return $this->render('front/products/order.html.twig', [
            'form' => $form,
            'produit' => $produit,
            'type' => $type,
            'unite_labels' => $unite_labels,
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

