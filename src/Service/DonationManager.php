<?php

namespace App\Service;

use App\Entity\Demande;
use InvalidArgumentException;

class DonationManager
{
    /**
     * Valide les règles métier d'une Demande de don (Module Donation).
     *
     * Règle 1 (adaptée) : La quantité demandée ne peut pas être négative ou nulle.
     * Règle 7 : Une opération (validation) ne peut être effectuée que si la demande est d'abord 'en_attente'.
     */
    public function validerDemande(Demande $demande): bool
    {
        if ($demande->getQuantiteDemandee() !== null && $demande->getQuantiteDemandee() <= 0) {
            throw new InvalidArgumentException("La quantité saisie ne peut pas être négative ou nulle.");
        }

        return true;
    }

    /**
     * Valide la transition de statut.
     * Règle 7: Une demande ne peut être acceptée que si elle était "En attente".
     */
    public function validerAcceptation(Demande $demande): bool
    {
        if ($demande->getStatut() !== Demande::STATUT_EN_ATTENTE) {
            throw new InvalidArgumentException("Une opération ne peut être validée que si l'étape précédente (en attente) est respectée.");
        }

        return true;
    }
}
