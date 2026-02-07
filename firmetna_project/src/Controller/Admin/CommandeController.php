<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commandes')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_admin_commandes', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAllOrderedByDate();
        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/statut', name: 'app_admin_commande_statut', methods: ['POST'])]
    public function updateStatut(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $statut = $request->request->get('statut');
        $allowed = [
            Commande::STATUT_EN_ATTENTE,
            Commande::STATUT_CONFIRMEE,
            Commande::STATUT_EXPEDIEE,
            Commande::STATUT_LIVREE,
            Commande::STATUT_ANNULEE,
        ];
        if ($this->isCsrfTokenValid('statut' . $commande->getId(), $request->request->get('_token')) && in_array($statut, $allowed, true)) {
            $commande->setStatut($statut);
            $entityManager->flush();
            $this->addFlash('success', 'Statut mis à jour.');
        }
        return $this->redirectToRoute('app_admin_commande_show', ['id' => $commande->getId()]);
    }
}
