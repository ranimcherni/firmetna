<?php

namespace App\Service;

use App\Entity\Event;
use InvalidArgumentException;

class EventManager
{
    /**
     * Valide les règles métier d'un Evénement.
     *
     * Règle 4 : Un champ obligatoire comme le nom de l'événement doit être rempli.
     * Règle additionnelle pour contexte : Les événements passés ne peuvent pas être créés.
     */
    public function validerEvent(Event $event): bool
    {
        if (empty($event->getNom())) {
            throw new InvalidArgumentException("Un champ obligatoire (nom) doit être rempli avant de soumettre.");
        }

        if ($event->getDate() !== null && $event->getDate() < new \DateTime()) {
            throw new InvalidArgumentException("La date de l'événement ne peut pas être dans le passé.");
        }

        return true;
    }
}
