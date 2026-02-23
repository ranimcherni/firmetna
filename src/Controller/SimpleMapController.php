<?php

namespace App\Controller\Admin;

use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimpleMapController extends AbstractController
{
    #[Route('/admin/simple-map', name: 'app_admin_simple_map')]
    public function index(PartnerRepository $partnerRepository): Response
    {
        $partners = $partnerRepository->findAll();
        
        return $this->render('admin/partner/show_map.html.twig', [
            'partners' => $partners
        ]);
    }
}
