<?php

namespace App\Tests\Service;

use App\Entity\Event;
use App\Service\EventManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EventManagerTest extends TestCase
{
    private EventManager $eventManager;

    protected function setUp(): void
    {
        $this->eventManager = new EventManager();
    }

    public function testValiderEventSucces(): void
    {
        $event = new Event();
        $event->setNom('Fête de la Moisson');
        $event->setDate(new \DateTime('+1 month'));

        $this->assertTrue($this->eventManager->validerEvent($event));
    }

    public function testNomObligatoire(): void
    {
        $event = new Event();
        $event->setNom(''); // Vide (Règle 4 violée)
        $event->setDate(new \DateTime('+1 month'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Un champ obligatoire (nom) doit être rempli avant de soumettre.");

        $this->eventManager->validerEvent($event);
    }

    public function testDateDansLePasse(): void
    {
        $event = new Event();
        $event->setNom('Marché');
        $event->setDate(new \DateTime('-1 day')); // Dans le passé

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La date de l'événement ne peut pas être dans le passé.");

        $this->eventManager->validerEvent($event);
    }
}
