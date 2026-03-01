<?php

namespace App\Tests\Service;

use App\Entity\Demande;
use App\Service\DonationManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DonationManagerTest extends TestCase
{
    private DonationManager $donationManager;

    protected function setUp(): void
    {
        $this->donationManager = new DonationManager();
    }

    public function testValiderDemandeSucces(): void
    {
        $demande = new Demande();
        $demande->setQuantiteDemandee(10);
        $demande->setStatut(Demande::STATUT_EN_ATTENTE);

        $this->assertTrue($this->donationManager->validerDemande($demande));
    }

    public function testQuantiteNePeutPasEtreNegative(): void
    {
        $demande = new Demande();
        $demande->setQuantiteDemandee(-5); // Règle 1 violée

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La quantité saisie ne peut pas être négative ou nulle.");

        $this->donationManager->validerDemande($demande);
    }

    public function testAccepterDemandeDejaRefuseeEchoue(): void
    {
        $demande = new Demande();
        $demande->setStatut(Demande::STATUT_REFUSEE); // Pas en attente (Règle 7 violée)

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Une opération ne peut être validée que si l'étape précédente (en attente) est respectée.");

        $this->donationManager->validerAcceptation($demande);
    }
}
