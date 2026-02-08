<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Afficher la page d'accueil pour tous les utilisateurs
        // Les admins peuvent acc├®der au back office via /admin/dashboard
        return $this->render('home/index.html.twig');
    }
}
