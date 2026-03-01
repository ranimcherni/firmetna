<?php

namespace App\Controller\Admin;

use App\Entity\Contract;
use App\Form\ContractType;
use App\Repository\ContractRepository;
use App\Repository\PartnerRepository;
use App\Service\PDFService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contracts')]
class ContractController extends AbstractController
{
    #[Route('/', name: 'app_admin_contract_index', methods: ['GET'])]
    public function index(Request $request, ContractRepository $offerRepository, PartnerRepository $partnerRepository): Response
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
        if ($type) {
            $qb->andWhere('o.type = :type')->setParameter('type', $type);
        }
        if ($status) {
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

        return $this->render('admin/contract/index.html.twig', [
            'offers' => $offers,
            'partners' => $partners,
        ]);
    }

    #[Route('/new', name: 'app_admin_contract_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PartnerRepository $partnerRepository): Response
    {
        $offer = new Contract();
        $partnerId = $request->query->get('partner_id');
        if ($partnerId) {
            $partner = $partnerRepository->find($partnerId);
            if ($partner) {
                $offer->setPartner($partner);
            }
        }

        $form = $this->createForm(ContractType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Contract créé avec succès.');
            return $this->redirectToRoute('app_admin_contract_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/contract/new.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export/csv', name: 'app_admin_contract_export_csv', methods: ['GET'])]
    public function exportCsv(ContractRepository $offerRepository): StreamedResponse
    {
        $offers = $offerRepository->findAllOrderByDate();

        $response = new StreamedResponse();
        $handle = fopen('php://output', 'w');
        
        foreach ($offers as $offer) {
            $partner = $offer->getPartner();
            fputcsv($handle, [
                'ID' => $offer->getId(),
                'Titre' => $offer->getTitle(),
                'Description' => $offer->getDescription(),
                'Type' => $offer->getType(),
                'Montant' => $offer->getAmount(),
                'Date offre' => $offer->getOfferDate() ? $offer->getOfferDate()->format('Y-m-d') : '',
                'Statut' => $offer->getStatus(),
                'Partenaire' => $partner ? $partner->getName() : '',
                'Date création' => $offer->getCreatedAt() ? $offer->getCreatedAt()->format('Y-m-d H:i') : '',
            ], ';');
        }

        fclose($handle);

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="contracts_' . date('Y-m-d') . '.csv"');

        return $response;
    }

    #[Route('/{id}/edit', name: 'app_admin_contract_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contract $offer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContractType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Contract mis à jour.');
            return $this->redirectToRoute('app_admin_contract_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/contract/edit.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_contract_delete', methods: ['POST'])]
    public function delete(Request $request, Contract $offer, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $offer->getId(), $token)) {
            $entityManager->remove($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Contract supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_contract_index');
    }

    #[Route('/{id}/pdf', name: 'app_admin_contract_pdf', methods: ['GET'])]
    public function generatePDF(Contract $contract, PDFService $pdfService): Response
    {
        return $pdfService->generateContractPDF($contract);
    }

    #[Route('/pdf/list', name: 'app_admin_contracts_pdf_list', methods: ['GET'])]
    public function generateListPDF(ContractRepository $contractRepository, PDFService $pdfService): Response
    {
        $contracts = $contractRepository->findAll();
        return $pdfService->generateContractsListPDF($contracts);
    }
}
