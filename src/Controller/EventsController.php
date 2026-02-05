<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenements')]
class EventsController extends AbstractController
{
    #[Route('/', name: 'app_events')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAllOrderByDate();

        return $this->render('front/events/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/{id}/participer', name: 'app_events_participer', methods: ['GET'])]
    public function participer(Event $event): Response
    {
        $this->addFlash('success', 'Participation confirmée !');

        return $this->redirectToRoute('app_events');
    }
}
