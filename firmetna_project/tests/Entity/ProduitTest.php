<?php

namespace App\Tests\Entity;

use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

class ProduitTest extends TestCase
{
    public function testProduitConstants(): void
    {
        self::assertSame('vegetale', Produit::TYPE_VEGETALE);
        self::assertSame('animale', Produit::TYPE_ANIMALE);
        self::assertSame('kilo', Produit::UNITE_KILO);
    }

    public function testProduitSettersGetters(): void
    {
        $p = new Produit();
        $p->setNom('Tomate');
        $p->setPrix('4.99');
        $p->setType(Produit::TYPE_VEGETALE);
        $p->setUnite(Produit::UNITE_KILO);
        $p->setStock(10);
        $p->setIsBio(true);
        $p->setBadge('Bio');

        self::assertSame('Tomate', $p->getNom());
        self::assertSame('4.99', $p->getPrix());
        self::assertSame(Produit::TYPE_VEGETALE, $p->getType());
        self::assertSame(10, $p->getStock());
        self::assertTrue($p->isBio());
        self::assertSame('Bio', $p->getBadge());
    }

    public function testProduitToString(): void
    {
        $p = new Produit();
        $p->setNom('Carotte');
        self::assertSame('Carotte', $p->__toString());
    }

    public function testProduitCreatedAt(): void
    {
        $p = new Produit();
        self::assertInstanceOf(\DateTimeImmutable::class, $p->getCreatedAt());
    }
}
