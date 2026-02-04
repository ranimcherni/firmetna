<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenements')]
class EventsController extends AbstractController
{
    #[Route('/', name: 'app_events')]
    public function index(): Response
    {
        return $this->render('front/placeholder.html.twig', [
            'module' => 'Événements',
            'icon' => 'fas fa-calendar-alt',
            'description' => 'Participez à nos foires, ateliers et rencontres agricoles.'
        ]);
    }
}
