<?php

namespace App\Service;

use App\Entity\User;
use InvalidArgumentException;

class UserManager
{
    /**
     * Valide les règles métier d'un Utilisateur.
     *
     * Règle 4 (adaptée) : Un champ obligatoire comme l'email doit être rempli.
     * Règle 9 : Le mot de passe doit contenir au moins 8 caractères.
     */
    public function validerUtilisateur(User $user): bool
    {
        if (empty($user->getEmail())) {
            throw new InvalidArgumentException("L'email est un champ obligatoire et doit être rempli.");
        }

        if ($user->getPassword() !== null && strlen($user->getPassword()) < 8) {
            throw new InvalidArgumentException("Le mot de passe doit contenir au moins 8 caractères.");
        }

        return true;
    }
}
