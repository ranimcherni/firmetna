<?php

namespace App\Service;

use App\Entity\Contract;
use InvalidArgumentException;

class ContractManager
{
    /**
     * Valide les règles métier d'un contrat de partenariat.
     *
     * Règle 3 : La date de fin d’un événement (ou contrat) doit être postérieure à la date de début.
     * Règle 5 (adaptée) : Le montant d'un contrat financier doit être strictement positif.
     */
    public function validerContrat(Contract $contract): bool
    {
        // Règle de validation des dates
        if ($contract->getDateFinContract() !== null && $contract->getDateDebutContract() !== null) {
            if ($contract->getDateFinContract() <= $contract->getDateDebutContract()) {
                throw new InvalidArgumentException("La date de fin doit être postérieure à la date de début.");
            }
        }

        // Règle de validation du montant financier
        if ($contract->getAmount() !== null && (float) $contract->getAmount() <= 0) {
            throw new InvalidArgumentException("Le montant d'un contrat financier doit être supérieur à zéro.");
        }

        return true;
    }
}
