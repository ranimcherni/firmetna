<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\DemandeRepository;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/donations')]
class DonationsController extends AbstractController
{
    public function __construct(
        private OffreRepository $offreRepository,
        private DemandeRepository $demandeRepository,
    ) {
    }

    #[Route('/', name: 'app_donations', methods: ['GET', 'POST'])]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offre->setDisponible(true);
            if ($this->getUser()) {
                $offre->setAuteur($this->getUser());
            }

            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
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

            $this->offreRepository->save($offre, true);
            $this->addFlash('success', 'Votre offre a ├®t├® publi├®e avec succ├¿s. Merci pour votre don !');
            return $this->redirectToRoute('app_donations');
        }

        $offres = $this->offreRepository->findAllOrderedByDate();
        $demandesByOffre = [];
        if ($this->getUser()) {
            foreach ($offres as $o) {
                $demandesByOffre[$o->getId()] = $this->demandeRepository->userADejaDemande($this->getUser(), $o);
            }
        }

        return $this->render('donations/index.html.twig', [
            'form' => $form,
            'offres' => $offres,
            'demandesByOffre' => $demandesByOffre,
        ]);
    }

    #[Route('/produit/{type}', name: 'app_donations_produit', methods: ['GET'], requirements: ['type' => 'legumes|fruits|engrais|semences'])]
    public function produit(string $type): Response
    {
        $infos = [
            'legumes' => [
                'titre' => 'L├®gumes',
                'sous_titre' => 'Disponible 15 kg tomates maintenant',
                'description' => 'Tomates, carottes, courgettes, laitue et autres l├®gumes frais du terroir. Cultiv├®s localement, ils sont id├®aux pour une alimentation saine et une agriculture de proximit├®.',
                'details' => 'Nos donateurs proposent des l├®gumes de saison, souvent issus de surplus ou de r├®coltes solidaires. Les quantit├®s varient selon les dons (kilos, cageots). Contactez le donateur via la liste des offres pour convenir d\'un retrait.',
                'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=1920',
                'icon' => 'fas fa-carrot',
            ],
            'fruits' => [
                'titre' => 'Fruits',
                'sous_titre' => 'Disponible 10 kilos pommes maintenant',
                'description' => 'Pommes, fraises, agrumes et fruits de saison. Des dons de producteurs et particuliers pour soutenir les agriculteurs et les familles dans le besoin.',
                'details' => 'Fruits frais ou en surplus, propos├®s en kg ou en caisses. Les vari├®t├®s et quantit├®s d├®pendent des dons. Consultez les offres des donateurs ci-dessous et faites votre demande.',
                'image' => 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&q=80&w=1920',
                'icon' => 'fas fa-apple-alt',
            ],
            'engrais' => [
                'titre' => 'Engrais',
                'sous_titre' => 'Disponible 5 sacs engrais organique maintenant',
                'description' => 'Engrais organique, compost et amendements pour les cultures. Des dons de coop├®ratives ou d\'agriculteurs pour favoriser une agriculture durable.',
                'details' => 'Sacs d\'engrais organique, compost ou autres intrants propos├®s par des donateurs. Quantit├®s et types variables. Faites une demande via le bouton ci-dessous pour ├¬tre mis en relation avec le donateur.',
                'image' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&q=80&w=1920',
                'icon' => 'fas fa-vial',
            ],
            'semences' => [
                'titre' => 'Semences',
                'sous_titre' => 'Disponible semences bl├® et orge maintenant',
                'description' => 'Semences de bl├®, orge, l├®gumes et cultures diverses. Des dons pour aider les agriculteurs ├á ensemencer leurs parcelles.',
                'details' => 'Sachets ou quantit├®s de semences (c├®r├®ales, mara├«chage) propos├®s par des donateurs. Id├®al pour d├®marrer une culture ou compl├®ter un stock. Consultez les offres et demandez via le formulaire des dons.',
                'image' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&q=80&w=1920',
                'icon' => 'fas fa-seedling',
            ],
        ];
        $info = $infos[$type] ?? $infos['legumes'];

        return $this->render('donations/produit_show.html.twig', [
            'type' => $type,
            'info' => $info,
        ]);
    }

    #[Route('/demander/{id}', name: 'app_donations_demander', methods: ['POST'])]
    public function demander(Request $request, Offre $offre): Response
    {
        if (!$this->isCsrfTokenValid('demander_' . $offre->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de s├®curit├® invalide.');
            return $this->redirectToRoute('app_donations');
        }
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Connectez-vous pour pouvoir demander une offre.');
            return $this->redirectToRoute('app_login');
        }

        if (!$offre->isDisponible()) {
            $this->addFlash('warning', 'Cette offre n\'est plus disponible.');
            return $this->redirectToRoute('app_donations');
        }

        if ($this->demandeRepository->userADejaDemande($this->getUser(), $offre)) {
            $this->addFlash('info', 'Vous avez d├®j├á demand├® cette offre.');
            return $this->redirectToRoute('app_donations');
        }

        $demande = new Demande();
        $demande->setOffre($offre);
        $demande->setDemandeur($this->getUser());
        $this->demandeRepository->save($demande, true);

        $this->addFlash('success', 'Votre demande a ├®t├® enregistr├®e. Le donateur vous contactera.');
        return $this->redirectToRoute('app_donations');
    }
}
