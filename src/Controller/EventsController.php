<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenements')]
class EventsController extends AbstractController
{
    #[Route('/', name: 'app_events')]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('front/events/index.html.twig', [
            'events' => $eventRepository->findBy([], ['date' => 'ASC']),
        ]);
    }

    #[Route('/participer/{id}', name: 'app_events_participer')]
    public function participer(Event $event): Response
    {
        // Simple placeholder for participation logic
        $this->addFlash('success', 'Votre participation à l\'événement "' . $event->getNom() . '" a été enregistrée !');
        return $this->redirectToRoute('app_events');
    }
}
