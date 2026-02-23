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
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/donations')]
class DonationsController extends AbstractController
{
    #[Route('/', name: 'app_donations', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        OffreRepository $offreRepository,
        DemandeRepository $demandeRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        $search = $request->query->get('q'); 
        $sort = $request->query->get('sort');

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move($this->getParameter('kernel.project_dir').'/public/uploads/dons', $newFilename);
                    $offre->setPhoto('uploads/dons/'.$newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image.");
                }
            }

            $offre->setDisponible(true);
            $offre->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre offre de don a ├®t├® publi├®e !');
            return $this->redirectToRoute('app_donations');
        }

        $offres = $offreRepository->findBySearchAndSort($search, $sort);

        $demandesByOffre = [];
        if ($user = $this->getUser()) {
            $userDemandes = $demandeRepository->findBy(['demandeur' => $user]);
            foreach ($userDemandes as $demande) {
                $demandesByOffre[$demande->getOffre()->getId()] = true;
            }
        }

        return $this->render('donations/index.html.twig', [
            'offres' => $offres,
            'form' => $form->createView(),
            'demandesByOffre' => $demandesByOffre,
            'q' => $search,
            'sort' => $sort,
        ]);
    }
#[Route('/demander/{id}', name: 'app_donations_demander', methods: ['POST'])]
public function demander(Request $request, Offre $offre, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) return $this->redirectToRoute('app_login');

    $qty = (int) $request->request->get('quantite_demandee');

    // 1. Validation Stock
    if ($qty <= 0 || $qty > $offre->getQuantite()) {
        $this->addFlash('danger', "Quantit├® indisponible.");
        return $this->redirectToRoute('app_donations');
    }

    // 2. Cr├®ation de la demande
    $demande = new Demande();
    $demande->setOffre($offre);
    $demande->setDemandeur($user);
    $demande->setQuantiteDemandee($qty);
    $demande->setStatut(Demande::STATUT_ACCEPTEE); 
    $demande->setCreatedAt(new \DateTimeImmutable()); // Assurez-vous d'avoir ce champ

    // 3. Mise ├á jour du stock
    $offre->setQuantite($offre->getQuantite() - $qty);
    if ($offre->getQuantite() === 0) {
        $offre->setDisponible(false);
    }

    $em->persist($demande);
    $em->flush();

    // 4. On stocke l'ID en session pour d├®clencher le t├®l├®chargement auto en JS
    $request->getSession()->set('download_pdf', $demande->getId());

    $this->addFlash('success', 'Demande enregistr├®e ! Votre bon de don va ├¬tre t├®l├®charg├®.');
    return $this->redirectToRoute('app_donations');
}

/**
 * Route s├®par├®e pour g├®n├®rer le PDF uniquement
 */
#[Route('/download-pdf/{id}', name: 'app_donations_pdf_download')]
public function downloadPdf(Demande $demande): Response
{
    // S├®curit├® : v├®rifier que l'utilisateur est bien le demandeur
    if ($demande->getDemandeur() !== $this->getUser()) {
        throw $this->createAccessDeniedException();
    }

    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->set('isRemoteEnabled', true); // Important si vous avez des images distantes
    
    $dompdf = new Dompdf($pdfOptions);
    $html = $this->renderView('donations/pdf_bon.html.twig', ['demande' => $demande]);
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="bon_de_don_' . $demande->getId() . '.pdf"'
    ]);
}
    // #[Route('/demander/{id}', name: 'app_donations_demander', methods: ['POST'])]
    // public function demander(Request $request, Offre $offre, EntityManagerInterface $em): Response
    // {
    //     $user = $this->getUser();
    //     if (!$user) return $this->redirectToRoute('app_login');

    //     $qty = (int) $request->request->get('quantite_demandee');

    //     // 1. Validation Stock
    //     if ($qty <= 0 || $qty > $offre->getQuantite()) {
    //         $this->addFlash('danger', "Quantit├® indisponible.");
    //         return $this->redirectToRoute('app_donations');
    //     }

    //     // 2. Cr├®ation de la demande (CORRECTION ICI : -> au lieu de .)
    //     $demande = new Demande();
    //     $demande->setOffre($offre);
    //     $demande->setDemandeur($user);
    //     $demande->setQuantiteDemandee($qty);
    //     $demande->setStatut(Demande::STATUT_ACCEPTEE); 

    //     // 3. Mise ├á jour du stock
    //     $offre->setQuantite($offre->getQuantite() - $qty);
    //     if ($offre->getQuantite() === 0) {
    //         $offre->setDisponible(false);
    //     }

    //     $em->persist($demande);
    //     $em->flush();

    //     // 4. G├®n├®ration du PDF
    //     $pdfOptions = new Options();
    //     $pdfOptions->set('defaultFont', 'Arial');
    //     $dompdf = new Dompdf($pdfOptions);
        
    //     $html = $this->renderView('donations/pdf_bon.html.twig', [
    //         'demande' => $demande
    //     ]);
        
    //     $dompdf->loadHtml($html);
    //     $dompdf->setPaper('A4', 'portrait');
    //     $dompdf->render();

    //     return new Response($dompdf->output(), 200, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'attachment; filename="bon_de_don_' . $demande->getId() . '.pdf"'
    //     ]);
    // }

    #[Route('/edit/{id}', name: 'app_donations_edit', methods: ['POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $data = $request->request->all('offre');

        if ($data) {
            $offre->setCategorie($data['categorie'] ?? $offre->getCategorie());
            $offre->setDescription($data['description'] ?? $offre->getDescription());
            $offre->setQuantite($data['quantite'] ?? $offre->getQuantite());
            $offre->setTelephone($data['telephone'] ?? $offre->getTelephone());

            $photoFile = $request->files->get('offre')['photoFile'] ?? null;
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($this->getParameter('kernel.project_dir').'/public/uploads/dons', $newFilename);
                $offre->setPhoto('uploads/dons/'.$newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Modification enregistr├®e avec succ├¿s !');
        }

        return $this->redirectToRoute('app_donations');
    }
#[Route('/produit/{type}', name: 'app_donations_produit', methods: ['GET', 'POST'])]
public function listByType(
    string $type, 
    Request $request,
    OffreRepository $offreRepository,
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger
): Response {
    $offre = new Offre();
    $form = $this->createForm(OffreType::class, $offre);
    $form->handleRequest($request);

    // G├®rer l'ajout d'un don m├¬me quand on est dans une cat├®gorie filtr├®e
    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photoFile')->getData();
        if ($photoFile) {
            $newFilename = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move($this->getParameter('kernel.project_dir').'/public/uploads/dons', $newFilename);
            $offre->setPhoto('uploads/dons/'.$newFilename);
        }
        $offre->setDisponible(true);
        $offre->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($offre);
        $entityManager->flush();

        $this->addFlash('success', 'Offre publi├®e !');
        return $this->redirectToRoute('app_donations_produit', ['type' => $type]);
    }

    $offres = $offreRepository->findBy(
        ['categorie' => ucfirst($type), 'disponible' => true],
        ['createdAt' => 'DESC']
    );

    return $this->render('donations/index.html.twig', [
        'offres' => $offres,
        'form' => $form->createView(),
        'demandesByOffre' => [],
        'q' => null,
        'sort' => null
    ]);
}
}
