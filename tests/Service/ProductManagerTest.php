<?php

namespace App\Tests\Service;

use App\Entity\Produit;
use App\Service\ProductManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductManagerTest extends TestCase
{
    private ProductManager $productManager;

    protected function setUp(): void
    {
        $this->productManager = new ProductManager();
    }

    public function testValiderProduitSucces(): void
    {
        $produit = new Produit();
        $produit->setStock(10);
        $produit->setPrix('15.50');

        $result = $this->productManager->validerProduit($produit);
        
        $this->assertTrue($result, "Le produit devrait être valide.");
    }

    public function testQuantiteNePeutPasEtreNegative(): void
    {
        $produit = new Produit();
        $produit->setStock(-5); // Règle 1 violée
        $produit->setPrix('10.00');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La quantité saisie ne peut pas être négative.");

        $this->productManager->validerProduit($produit);
    }

    public function testPrixDoitEtreSuperieurAZero(): void
    {
        $produit = new Produit();
        $produit->setStock(5);
        $produit->setPrix('0.00'); // Règle 2 violée

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le prix d'un article doit toujours être supérieur à zéro.");

        $this->productManager->validerProduit($produit);
    }
}
