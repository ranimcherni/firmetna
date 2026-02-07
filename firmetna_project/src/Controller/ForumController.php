<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum')]
    public function index(): Response
    {
        return $this->render('front/placeholder.html.twig', [
            'module' => 'Forum Communautaire',
            'icon' => 'fas fa-comments',
            'description' => 'Échangez avec la communauté, posez vos questions et partagez votre savoir-faire.'
        ]);
    }
}
