<?php

namespace App\Controller\Admin;

use App\Entity\Offre;
use App\Entity\Demande;

use App\Form\OffreAdminType;
use App\Repository\OffreRepository;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
<<<<<<< HEAD
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\TwilioService;
use Dompdf\Dompdf;
use Dompdf\Options;
=======
>>>>>>> gestion-produit

#[Route('/admin')]
class DonationController extends AbstractController
{
    public function __construct(
        private OffreRepository $offreRepository,
        private DemandeRepository $demandeRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/donations', name: 'app_admin_donations', methods: ['GET'])]
    public function index(): Response
    {
        $offres = $this->offreRepository->findAllOrderedByDate();

        return $this->render('admin/donation/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/donation/new', name: 'app_admin_donation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreAdminType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offre->setDisponible((bool) $form->get('disponible')->getData());
            $this->handlePhotoUpload($form, $offre, $slugger);
            $this->offreRepository->save($offre, true);
            $this->addFlash('success', 'Offre de don ajoutée avec succès.');
            return $this->redirectToRoute('app_admin_donations');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/donation/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/donation/{id}/edit', name: 'app_admin_donation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(OffreAdminType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offre->setDisponible((bool) $form->get('disponible')->getData());
            $this->handlePhotoUpload($form, $offre, $slugger);
            $this->offreRepository->save($offre, true);
            $this->addFlash('success', 'Offre de don modifiée.');
            return $this->redirectToRoute('app_admin_donations');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/donation/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/donation/{id}', name: 'app_admin_donation_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offre->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($offre);
            $this->entityManager->flush();
            $this->addFlash('success', 'Offre de don supprimée.');
        }

        return $this->redirectToRoute('app_admin_donations');
    }

    #[Route('/donations/demandes', name: 'app_admin_donation_demandes', methods: ['GET'])]
    public function demandes(): Response
    {
        $demandes = $this->demandeRepository->findAllOrderedByDate();

        return $this->render('admin/donation/demandes.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/donations/demande/{id}/delete', name: 'app_admin_donation_demande_delete', methods: ['POST'])]
    public function deleteDemande(Request $request, Demande $demande): Response
    {
        if ($this->isCsrfTokenValid('delete_demande' . $demande->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($demande);
            $this->entityManager->flush();
            $this->addFlash('success', 'Demande de don supprimée.');
        }

        return $this->redirectToRoute('app_admin_donation_demandes');
    }

    #[Route('/donations/demande/new', name: 'app_admin_donation_demande_new', methods: ['GET', 'POST'])]
    public function demandeNew(Request $request): Response
    {
        $demande = new Demande();
        $demande->setCreatedAt(new \DateTimeImmutable());
        $form = $this->createForm(\App\Form\DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($demande);
            $this->entityManager->flush();

            $this->addFlash('success', 'Demande de don ajoutée avec succès.');
            return $this->redirectToRoute('app_admin_donation_demandes');
        }

        return $this->render('admin/donation/demande_new.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/donations/demande/{id}/edit', name: 'app_admin_donation_demande_edit', methods: ['GET', 'POST'])]
    public function demandeEdit(Request $request, Demande $demande): Response
    {
        $form = $this->createForm(\App\Form\DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Demande de don modifiée.');
            return $this->redirectToRoute('app_admin_donation_demandes');
        }

        return $this->render('admin/donation/demande_edit.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
        ]);
    }

    private function handlePhotoUpload(FormInterface $form, Offre $offre, SluggerInterface $slugger): void
    {
        $photoFile = $form->get('photoFile')->getData();
        if (!$photoFile) {
            return;
        }
        $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/dons';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $photoFile->move($uploadDir, $newFilename);
        $offre->setPhoto('/uploads/dons/' . $newFilename);
    }
<<<<<<< HEAD
#[Route('/donations/stats', name: 'app_admin_donations_stats')]
public function statistics(OffreRepository $offreRepo, DemandeRepository $demandeRepo): Response
{
    return $this->render('admin/donation/stats.html.twig', [
        'totalOffres' => $offreRepo->count([]),
        'totalDemandes' => $demandeRepo->count([]),
        'topFrequency' => $offreRepo->findTopDonorsByFrequency(5),
        'topQuantity' => $offreRepo->findTopDonorsByQuantity(5),
        'statsCategory' => $offreRepo->getStatsByCategory(),
    ]);
}



#[Route('/donations/stats/pdf', name: 'app_admin_donations_stats_pdf')]
public function downloadPdf(OffreRepository $offreRepo): Response
{
    // 1. Récupération des données identiques aux stats
    $topQuantity = $offreRepo->findTopDonorsByQuantity(5);
    $statsCategory = $offreRepo->getStatsByCategory();

    // 2. Configuration de Dompdf
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->set('isRemoteEnabled', true); // Important pour charger les images/styles

    $dompdf = new Dompdf($pdfOptions);

    // 3. Génération du HTML via Twig
    $html = $this->renderView('admin/donation/stats_pdf.html.twig', [
        'topQuantity' => $topQuantity,
        'statsCategory' => $statsCategory,
        'date' => new \DateTime(),
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 4. Envoi du fichier au navigateur
    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="statistiques-firmetna.pdf"'
    ]);
}
#[Route('/send-sms', name: 'admin_send_sms', methods: ['POST'])]
public function sendSms(
    Request $request,
    TwilioService $twilio
): JsonResponse {

    $data = json_decode($request->getContent(), true);

    if (!$data || empty($data['phone']) || empty($data['message'])) {
        return new JsonResponse(['error' => 'Données invalides'], 400);
    }

    try {
        $twilio->sendSms($data['phone'], $data['message']);
        return new JsonResponse(['success' => 'SMS envoyé avec succès !']);
    } catch (\Exception $e) {
        return new JsonResponse([
            'error' => 'Erreur Twilio: ' . $e->getMessage()
        ], 500);
    }
}
=======
>>>>>>> gestion-produit
}
