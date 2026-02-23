<?php

namespace App\Service;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $produitRepository;

    public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository)
    {
        $this->requestStack = $requestStack;
        $this->produitRepository = $produitRepository;
    }

    public function add(int $productId, array $data = [])
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        // If the cart is in the old format, reset it to avoid errors
        if (!empty($cart) && !is_array(reset($cart))) {
            $cart = [];
        }

        // If data is empty, we check if there's already an item with this product ID 
        // that has NO metadata, and increment it.
        if (empty($data['address']) && empty($data['comment'])) {
            foreach ($cart as $uniqueId => $item) {
                if (is_array($item) && isset($item['productId']) && $item['productId'] === $productId && empty($item['address']) && empty($item['comment'])) {
                    $cart[$uniqueId]['quantity'] += ($data['quantity'] ?? 1);
                    $session->set('cart', $cart);
                    return;
                }
            }
        }

        $uniqueId = uniqid('cart_', true);
        $cart[$uniqueId] = array_merge([
            'productId' => $productId,
            'quantity' => $data['quantity'] ?? 1,
            'address' => $data['address'] ?? '',
            'comment' => $data['comment'] ?? ''
        ], $data);

        $session->set('cart', $cart);
    }

    public function increaseQuantity(string $uniqueId)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$uniqueId])) {
            $cart[$uniqueId]['quantity']++;
        }

        $session->set('cart', $cart);
    }

    public function remove(string $uniqueId)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$uniqueId])) {
            if ($cart[$uniqueId]['quantity'] > 1) {
                $cart[$uniqueId]['quantity']--;
            } else {
                unset($cart[$uniqueId]);
            }
        }

        $session->set('cart', $cart);
    }

    public function delete(string $uniqueId)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$uniqueId])) {
            unset($cart[$uniqueId]);
        }

        $session->set('cart', $cart);
    }

    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        $fullCart = [];

        foreach ($cart as $uniqueId => $item) {
            if (!is_array($item) || !isset($item['productId'])) {
                continue;
            }

            $produit = $this->produitRepository->find($item['productId']);
            if ($produit) {
                $item['produit'] = $produit;
                $item['uniqueId'] = $uniqueId;
                $fullCart[] = $item;
            }
        }

        return $fullCart;
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getFullCart() as $item) {
            $total += $item['produit']->getPrix() * $item['quantity'];
        }

        return $total;
    }

    public function getItemsCount(): int
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        
        $count = 0;
        foreach ($cart as $item) {
            if (is_array($item) && isset($item['quantity'])) {
                $count += $item['quantity'];
            }
        }
        
        return $count;
    }

    public function clear()
    {
        $this->requestStack->getSession()->remove('cart');
    }
}
