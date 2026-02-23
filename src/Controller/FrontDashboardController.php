<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_front_dashboard')]
    public function index(): Response
    {
        return $this->render('front/dashboard.html.twig');
    }
}
