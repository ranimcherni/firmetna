<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/lieu')]
class LieuController extends AbstractController
{
    #[Route('/', name: 'app_admin_lieu_index', methods: ['GET'])]
    public function index(Request $request, LieuRepository $lieuRepository): Response
    {
        $q = $request->query->get('q');
        
        $queryBuilder = $lieuRepository->createQueryBuilder('l');

        if ($q) {
            $queryBuilder->andWhere('l.ville LIKE :q OR l.adresse LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        $lieux = $queryBuilder->getQuery()->getResult();

        return $this->render('admin/lieu/index.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    #[Route('/new', name: 'app_admin_lieu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/lieux';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                try {
                    $imageFile->move($uploadDir, $newFilename);
                    $lieu->setImage('/uploads/lieux/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu créé avec succès !');
            return $this->redirectToRoute('app_admin_lieu_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Erreur de validation. Veuillez vérifier les champs du formulaire.');
        }

        return $this->render('admin/lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_lieu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lieu $lieu, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/lieux';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                try {
                    $imageFile->move($uploadDir, $newFilename);
                    $lieu->setImage('/uploads/lieux/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }
            $entityManager->flush();

            $this->addFlash('success', 'Lieu modifié avec succès !');
            return $this->redirectToRoute('app_admin_lieu_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Erreur de validation. Veuillez vérifier les champs du formulaire.');
        }

        return $this->render('admin/lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_lieu_delete', methods: ['POST'])]
    public function delete(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Lieu supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_lieu_index');
    }
}
