<?php

namespace App\Controller\Admin;

use App\Entity\PartnerOffer;
use App\Form\PartnerOfferType;
use App\Repository\PartnerOfferRepository;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/partenariats/offres')]
class PartnerOfferController extends AbstractController
{
    #[Route('/', name: 'app_admin_partner_offer_index', methods: ['GET'])]
    public function index(Request $request, PartnerOfferRepository $offerRepository, PartnerRepository $partnerRepository): Response
    {
        $q = $request->query->get('q');
        $partnerId = $request->query->get('partner_id');
        $type = $request->query->get('type');
        $status = $request->query->get('status');
        $sort = $request->query->get('sort', 'date_desc');

        $qb = $offerRepository->createQueryBuilder('o')
            ->leftJoin('o.partner', 'p')
            ->addSelect('p');

        if ($q) {
            $qb->andWhere('o.title LIKE :q OR o.description LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }
        if ($partnerId) {
            $qb->andWhere('p.id = :partnerId')->setParameter('partnerId', $partnerId);
        }
        if ($type && $type !== '') {
            $qb->andWhere('o.type = :type')->setParameter('type', $type);
        }
        if ($status && $status !== '') {
            $qb->andWhere('o.status = :status')->setParameter('status', $status);
        }

        switch ($sort) {
            case 'date_asc':
                $qb->orderBy('o.offerDate', 'ASC')->addOrderBy('o.createdAt', 'ASC');
                break;
            case 'title':
                $qb->orderBy('o.title', 'ASC');
                break;
            case 'partner':
                $qb->orderBy('p.name', 'ASC');
                break;
            case 'status':
                $qb->orderBy('o.status', 'ASC');
                break;
            default:
                $qb->orderBy('o.offerDate', 'DESC')->addOrderBy('o.createdAt', 'DESC');
        }

        $offers = $qb->getQuery()->getResult();
        $partners = $partnerRepository->findAllOrderedByName();

        return $this->render('admin/partner_offer/index.html.twig', [
            'offers' => $offers,
            'partners' => $partners,
        ]);
    }

    #[Route('/new', name: 'app_admin_partner_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PartnerRepository $partnerRepository): Response
    {
        $offer = new PartnerOffer();
        $partnerId = $request->query->get('partner_id');
        if ($partnerId) {
            $partner = $partnerRepository->find($partnerId);
            if ($partner) {
                $offer->setPartner($partner);
            }
        }

        $form = $this->createForm(PartnerOfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Offre créée avec succès.');
            return $this->redirectToRoute('app_admin_partner_offer_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/partner_offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export/csv', name: 'app_admin_partner_offer_export_csv', methods: ['GET'])]
    public function exportCsv(PartnerOfferRepository $offerRepository): StreamedResponse
    {
        $offers = $offerRepository->findAllOrderByDate();

        $response = new StreamedResponse(function () use ($offers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Partenaire', 'Type', 'Titre', 'Description', 'Montant', 'Date offre', 'Statut', 'Créé le'], ';');
            foreach ($offers as $o) {
                fputcsv($handle, [
                    $o->getId(),
                    $o->getPartner() ? $o->getPartner()->getName() : '',
                    $o->getType(),
                    $o->getTitle(),
                    $o->getDescription(),
                    $o->getAmount(),
                    $o->getOfferDate() ? $o->getOfferDate()->format('Y-m-d') : '',
                    $o->getStatus(),
                    $o->getCreatedAt() ? $o->getCreatedAt()->format('Y-m-d H:i') : '',
                ], ';');
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="offres_partenaires_' . date('Y-m-d') . '.csv"');

        return $response;
    }

    #[Route('/{id}/edit', name: 'app_admin_partner_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PartnerOffer $offer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartnerOfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Offre mise à jour.');
            return $this->redirectToRoute('app_admin_partner_offer_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/partner_offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_partner_offer_delete', methods: ['POST'])]
    public function delete(Request $request, PartnerOffer $offer, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $offer->getId(), $token)) {
            $entityManager->remove($offer);
            $entityManager->flush();
            $this->addFlash('success', 'Offre supprimée.');
        }

        return $this->redirectToRoute('app_admin_partner_offer_index');
    }
}
