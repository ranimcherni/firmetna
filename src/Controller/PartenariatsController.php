<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/partenariats-front')]
class PartenariatsController extends AbstractController
{
    #[Route('/', name: 'app_partenariats')]
    public function index(): Response
    {
        return $this->render('front/placeholder.html.twig', [
            'module' => 'Partenariats',
            'icon' => 'fas fa-handshake',
            'description' => 'Cr├®ez des liens durables entre producteurs et partenaires ├®conomiques.'
        ]);
    }
}
