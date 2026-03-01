<?php

namespace App\Tests\Service;

use App\Entity\Publication;
use App\Entity\User;
use App\Service\PublicationManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PublicationManagerTest extends TestCase
{
    private PublicationManager $publicationManager;

    protected function setUp(): void
    {
        $this->publicationManager = new PublicationManager();
    }

    public function testValiderPublicationSucces(): void
    {
        $publication = new Publication();
        $publication->setTitre('Nouvelle idée pour la ferme');
        $publication->setAuteur(new User()); // Auteur présent

        $this->assertTrue($this->publicationManager->validerPublication($publication));
    }

    public function testTitreObligatoire(): void
    {
        $publication = new Publication();
        $publication->setTitre('   '); // Vide ou espaces (Règle 4 violée)
        $publication->setAuteur(new User());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le titre est obligatoire avant de soumettre.");

        $this->publicationManager->validerPublication($publication);
    }

    public function testAuteurObligatoirePourPublier(): void
    {
        $publication = new Publication();
        $publication->setTitre('Problème technique');
        // Aucun auteur défini (Règle 10 violée)

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Une publication ne peut être effectuée qu'avec un auteur défini.");

        $this->publicationManager->validerPublication($publication);
    }
}
