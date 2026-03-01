<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panier')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('front/cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    #[Route('/add-product/{id}', name: 'cart_add')]
    public function add(int $id, CartService $cartService): Response
    {
        $cartService->add($id);
        $this->addFlash('success', 'Produit ajouté au panier !');
        
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/increase/{uniqueId}', name: 'cart_increase')]
    public function increase(string $uniqueId, CartService $cartService): Response
    {
        $cartService->increaseQuantity($uniqueId);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{uniqueId}', name: 'cart_remove')]
    public function remove(string $uniqueId, CartService $cartService): Response
    {
        $cartService->remove($uniqueId);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{uniqueId}', name: 'cart_delete')]
    public function delete(string $uniqueId, CartService $cartService): Response
    {
        $cartService->delete($uniqueId);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/valider', name: 'cart_checkout')]
    public function checkout(CartService $cartService, EntityManagerInterface $em): Response
    {
        $items = $cartService->getFullCart();
        
        if (empty($items)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_products');
        }

        $commande = new Commande();
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
            $commande->setClient($user);
        }
        
        // Commande entity seems to have its own way of setting defaults, but let's be safe.
        $commande->setDateCommande(new \DateTimeImmutable());
        $commande->setStatut('En attente');

        foreach ($items as $item) {
            $produit = $item['produit'];
            $quantity = $item['quantity'];

            if ($produit->getStock() < $quantity) {
                $this->addFlash('error', "Stock insuffisant pour le produit {$produit->getNom()}");
                return $this->redirectToRoute('cart_index');
            }

            $ligne = new LigneCommande();
            $ligne->setProduit($produit);
            $ligne->setQuantite($quantity);
            $ligne->setPrixUnitaire($produit->getPrix());
            $ligne->setCommande($commande);

            // If we want to store the item-specific address/comment in the line item:
            // But usually LigneCommande doesn't have these. 
            // If the user wants separate addresses per product, we might need to create separate Commande objects or extend LigneCommande.
            // For now, I'll use the last address/comment provided for the whole Commande, 
            // or we can just ignore them if they are only for the cart.
            // Let's check if Commande has these methods.
            if ($item['address']) {
                $commande->setAdresseLivraison($item['address']);
            }
            if ($item['comment']) {
                $commande->setCommentaire($item['comment']);
            }

            $commande->addLigne($ligne);
            
            // Decrement stock
            $produit->setStock($produit->getStock() - $quantity);
            $em->persist($produit);
            $em->persist($ligne);
        }

        $commande->recalculerTotal();
        
        $em->persist($commande);
        $em->flush();

        $cartService->clear();
        $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');

        return $this->redirectToRoute('app_front_dashboard');
    }
}
