<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/partenariats-front')]
class PartenariatsController extends AbstractController
{
    #[Route('/', name: 'app_partenariats')]
    public function index(PartnerRepository $partnerRepository): Response
    {
        $partners = $partnerRepository->findAllOrderedByName();

        return $this->render('partenariats/index.html.twig', [
            'partners' => $partners,
        ]);
    }

    #[Route('/{id}', name: 'app_partenariats_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Partner $partner): Response
    {
        return $this->render('partenariats/show.html.twig', [
            'partner' => $partner,
        ]);
    }
}
