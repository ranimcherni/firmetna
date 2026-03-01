<?php

namespace App\Service;

use App\Entity\Produit;
use InvalidArgumentException;

class ProductManager
{
    /**
     * Valide les règles métier d'un produit.
     *
     * Règle 1 : La quantité saisie ne peut pas être négative.
     * Règle 2 : Le prix d'un article doit toujours être supérieur à zéro.
     */
    public function validerProduit(Produit $produit): bool
    {
        if ($produit->getStock() < 0) {
            throw new InvalidArgumentException("La quantité saisie ne peut pas être négative.");
        }

        if ((float) $produit->getPrix() <= 0) {
            throw new InvalidArgumentException("Le prix d'un article doit toujours être supérieur à zéro.");
        }

        return true;
    }
}
