<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/donations')]
class DonationsController extends AbstractController
{
    #[Route('/', name: 'app_donations', methods: ['GET', 'POST'])]
    public function index(Request $request, OffreRepository $offreRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
                try {
                    $photoFile->move($this->getParameter('kernel.project_dir').'/public/uploads/dons', $newFilename);
                    $offre->setPhoto('/uploads/dons/'.$newFilename);
                } catch (\Exception $e) {}
            }

            $offre->setDisponible(true);
            $offre->setCreatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre offre de don a été publiée !');
            return $this->redirectToRoute('app_donations');
        }

        return $this->render('donations/index.html.twig', [
            'offres' => $offreRepository->findBy(['disponible' => true], ['createdAt' => 'DESC']),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{type}', name: 'app_donations_produit')]
    public function listByType(string $type, OffreRepository $offreRepository): Response
    {
        return $this->render('donations/index.html.twig', [
            'offres' => $offreRepository->findBy(['categorie' => ucfirst($type), 'disponible' => true], ['createdAt' => 'DESC']),
            'form' => $this->createForm(OffreType::class)->createView(),
        ]);
    }

    #[Route('/demander/{id}', name: 'app_donations_demander', methods: ['POST'])]
    public function demander(int $id, Offre $offre, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('demander_'.$offre->getId(), $request->request->get('_token'))) {
            $demande = new Demande();
            $demande->setOffre($offre);
            $demande->setDemandeur($this->getUser());
            $demande->setCreatedAt(new \DateTimeImmutable());
            $demande->setStatut(Demande::STATUT_EN_ATTENTE);

            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a été envoyée au donateur.');
        }

        return $this->redirectToRoute('app_donations');
    }
}
