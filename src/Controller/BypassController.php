<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BypassController extends AbstractController
{
    #[Route('/admin-bypass', name: 'app_admin_bypass')]
    public function bypass(): Response
    {
        // Direct access to admin dashboard for presentation
        return $this->forward('App\Controller\Admin\DashboardController::index');
    }
}
