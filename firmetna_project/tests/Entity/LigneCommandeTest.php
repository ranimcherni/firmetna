<?php

namespace App\Tests\Entity;

use App\Entity\LigneCommande;
use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

class LigneCommandeTest extends TestCase
{
    public function testSousTotal(): void
    {
        $ligne = new LigneCommande();
        $ligne->setQuantite(3);
        $ligne->setPrixUnitaire('10.00');
        self::assertSame('30.00', $ligne->getSousTotal());
    }

    public function testSousTotalWithDecimals(): void
    {
        $ligne = new LigneCommande();
        $ligne->setQuantite(2);
        $ligne->setPrixUnitaire('4.99');
        self::assertSame('9.98', $ligne->getSousTotal());
    }

    public function testSousTotalWhenNullReturnsZero(): void
    {
        $ligne = new LigneCommande();
        self::assertSame('0', $ligne->getSousTotal());
    }
}
