<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $produits = [
            ['Tomates biologiques', 'Tomates fraîches du terroir, cultivées sans pesticides', '4.99', Produit::TYPE_VEGETALE, Produit::UNITE_KILO, 'Bio'],
            ['Carottes fraîches', 'Carottes croquantes et sucrées de nos champs', '3.50', Produit::TYPE_VEGETALE, Produit::UNITE_KILO, 'Nouveau'],
            ['Pommes Golden', 'Pommes douces et juteuses, parfaites pour toute la famille', '5.20', Produit::TYPE_VEGETALE, Produit::UNITE_KILO, 'Bio'],
            ['Laitue romaine', 'Laitue croquante du jour, idéale pour vos salades', '2.80', Produit::TYPE_VEGETALE, Produit::UNITE_UNITE, 'Frais'],
            ['Courgettes vertes', 'Courgettes tendres et savoureuses de saison', '3.90', Produit::TYPE_VEGETALE, Produit::UNITE_KILO, 'Bio'],
            ['Fraises de saison', 'Fraises sucrées et parfumées, fraîchement cueillies', '6.50', Produit::TYPE_VEGETALE, Produit::UNITE_BARQUETTE, 'Promo'],
            ['6 œufs biologiques', 'Œufs frais de poules élevées en plein air', '4.99', Produit::TYPE_ANIMALE, Produit::UNITE_BOITE, 'Bio'],
            ['12 œufs biologiques', 'Boîte de 12 œufs fermiers de qualité', '9.99', Produit::TYPE_ANIMALE, Produit::UNITE_BOITE, 'Bio'],
            ['Poulets de chair', 'Poulet blanc élevé en plein air, alimentation naturelle', '9.99', Produit::TYPE_ANIMALE, Produit::UNITE_KILO, 'Fermier'],
            ['Pondeuses', 'Poule pondeuse rousse, excellente production d\'œufs', '9.99', Produit::TYPE_ANIMALE, Produit::UNITE_UNITE, 'Productif'],
        ];
        foreach ($produits as $p) {
            $produit = new Produit();
            $produit->setNom($p[0])->setDescription($p[1])->setPrix($p[2])->setType($p[3])->setUnite($p[4])->setBadge($p[5]);
            $produit->setStock(50);
            $produit->setIsBio($p[5] === 'Bio');
            $manager->persist($produit);
        }
        $manager->flush();
    }
}
