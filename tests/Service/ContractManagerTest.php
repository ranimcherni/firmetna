<?php

namespace App\Tests\Service;

use App\Entity\Contract;
use App\Service\ContractManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ContractManagerTest extends TestCase
{
    private ContractManager $contractManager;

    protected function setUp(): void
    {
        $this->contractManager = new ContractManager();
    }

    public function testValiderContratSucces(): void
    {
        $contract = new Contract();
        $contract->setDateDebutContract(new \DateTime('2026-01-01'));
        $contract->setDateFinContract(new \DateTime('2026-12-31'));
        $contract->setAmount('1500.00');

        $this->assertTrue($this->contractManager->validerContrat($contract));
    }

    public function testDateFinDoitEtrePosterieureDateDebut(): void
    {
        $contract = new Contract();
        $contract->setDateDebutContract(new \DateTime('2026-12-31'));
        $contract->setDateFinContract(new \DateTime('2026-01-01')); // Fin avant le début (Règle 3 violée)

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La date de fin doit être postérieure à la date de début.");

        $this->contractManager->validerContrat($contract);
    }

    public function testMontantDoitEtreSuperieurAZero(): void
    {
        $contract = new Contract();
        $contract->setAmount('-50.00'); // Montant négatif (Règle 5 violée)

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le montant d'un contrat financier doit être supérieur à zéro.");

        $this->contractManager->validerContrat($contract);
    }
}
