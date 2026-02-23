<?php

namespace App\Controller\Admin;

use App\Entity\Partner;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/partenariats')]
class PartnerController extends AbstractController
{
    #[Route('/', name: 'app_admin_partenariats', methods: ['GET'])]
    public function index(Request $request, PartnerRepository $partnerRepository): Response
    {
        $q = $request->query->get('q');
        $status = $request->query->get('status');

        $queryBuilder = $partnerRepository->createQueryBuilder('p');

        if ($q) {
            $queryBuilder->andWhere('p.name LIKE :q OR p.email LIKE :q OR p.description LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($status && $status !== 'Tous') {
            $queryBuilder->andWhere('p.status = :status')
                ->setParameter('status', $status);
        }

        $partners = $queryBuilder->orderBy('p.name', 'ASC')->getQuery()->getResult();

        return $this->render('admin/partner/index.html.twig', [
            'partners' => $partners,
        ]);
    }

    #[Route('/new', name: 'app_admin_partner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $partner = new Partner();
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partner);
            $entityManager->flush();

            $this->addFlash('success', 'Partenaire créé avec succès.');
            return $this->redirectToRoute('app_admin_partenariats');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_partner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partner $partner, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Partenaire mis à jour.');
            return $this->redirectToRoute('app_admin_partenariats');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export/csv', name: 'app_admin_partner_export_csv', methods: ['GET'])]
    public function exportCsv(PartnerRepository $partnerRepository): StreamedResponse
    {
        $partners = $partnerRepository->findAllOrderedByName();

        $response = new StreamedResponse(function () use ($partners) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nom', 'Type', 'Email', 'Téléphone', 'Adresse', 'Statut', 'Créé le'], ';');
            foreach ($partners as $p) {
                fputcsv($handle, [
                    $p->getId(),
                    $p->getName(),
                    $p->getType(),
                    $p->getEmail(),
                    $p->getPhone(),
                    $p->getAddress(),
                    $p->getStatus(),
                    $p->getCreatedAt() ? $p->getCreatedAt()->format('Y-m-d H:i') : '',
                ], ';');
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="partenaires_' . date('Y-m-d') . '.csv"');

        return $response;
    }

    #[Route('/{id}', name: 'app_admin_partner_delete', methods: ['POST'])]
    public function delete(Request $request, Partner $partner, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $partner->getId(), $token)) {
            $entityManager->remove($partner);
            $entityManager->flush();
            $this->addFlash('success', 'Partenaire supprimé.');
        }

        return $this->redirectToRoute('app_admin_partenariats');
    }
}
