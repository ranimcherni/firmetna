<?php

namespace App\Service;

use App\Entity\Publication;
use InvalidArgumentException;

class PublicationManager
{
    /**
     * Valide les règles métier d'une Publication (Forum).
     *
     * Règle 4 (adaptée) : Un champ obligatoire (titre) doit être rempli avant de soumettre.
     * Règle 10 (adaptée) : Une action critique (créer une publication) ne peut être effectuée qu'avec une "autorisation" (ici, un auteur doit exister).
     */
    public function validerPublication(Publication $publication): bool
    {
        if (empty(trim((string) $publication->getTitre()))) {
            throw new InvalidArgumentException("Le titre est obligatoire avant de soumettre.");
        }

        if ($publication->getAuteur() === null) {
            throw new InvalidArgumentException("Une publication ne peut être effectuée qu'avec un auteur défini.");
        }

        return true;
    }
}
