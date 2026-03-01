<?php

namespace App\Controller\Admin;

use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/publication')]
class PublicationController extends AbstractController
{
    #[Route('/', name: 'app_admin_publication_index', methods: ['GET'])]
    public function index(Request $request, PublicationRepository $publicationRepository): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $publicationRepository->createQueryBuilder('p')
            ->leftJoin('p.auteur', 'u')
            ->addSelect('u')
            ->orderBy('p.dateCreation', 'DESC');

        if ($q) {
            $queryBuilder->andWhere('p.titre LIKE :q OR p.contenu LIKE :q OR u.nom LIKE :q OR u.prenom LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        return $this->render('admin/publication/index.html.twig', [
            'publications' => $queryBuilder->getQuery()->getResult(),
        ]);
    }

    #[Route('/new', name: 'app_admin_publication_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('publications_directory'),
                        $newFilename
                    );
                    $publication->setImageFilename($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            if (!$publication->getDateCreation()) {
                $publication->setDateCreation(new \DateTimeImmutable());
            }

            // In admin, we might want to manually set the author or use the current user
            if (!$publication->getAuteur()) {
                $user = $this->getUser();
                if ($user instanceof \App\Entity\User) {
                    $publication->setAuteur($user);
                }
            }

            $entityManager->persist($publication);
            $entityManager->flush();

            $this->addFlash('success', 'Publication créée avec succès !');
            return $this->redirectToRoute('app_admin_publication_index');
        }

        return $this->render('admin/publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_publication_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publication $publication, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('publications_directory'),
                        $newFilename
                    );
                    $publication->setImageFilename($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Publication modifiée !');
            return $this->redirectToRoute('app_admin_publication_index');
        }

        return $this->render('admin/publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_publication_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            $entityManager->remove($publication);
            $entityManager->flush();
            $this->addFlash('success', 'Publication supprimée !');
        }

        return $this->redirectToRoute('app_admin_publication_index');
    }
}
