<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/forum')]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum')]
    public function index(Request $request, PublicationRepository $publicationRepository, PaginatorInterface $paginator): Response
    {
        $query = $publicationRepository->findAllOrderByDateDescQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6 // limit per page
        );

        return $this->render('forum/index.html.twig', [
            'publications' => $pagination,
        ]);
    }

    #[Route('/nouveau', name: 'app_forum_new', methods: ['GET', 'POST'])]
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
                    $imageFile->move($this->getParameter('publications_directory'), $newFilename);
                    $publication->setImageFilename($newFilename);
                } catch (\Exception $e) {}
            }

            $publication->setDateCreation(new \DateTimeImmutable());
            $publication->setAuteur($this->getUser());

            $entityManager->persist($publication);
            $entityManager->flush();

            $this->addFlash('success', 'Votre publication a été partagée !');
            return $this->redirectToRoute('app_forum');
        }

        return $this->render('forum/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voir/{id}', name: 'app_forum_show', methods: ['GET', 'POST'])]
    public function show(int $id, Request $request, PublicationRepository $publicationRepository, EntityManagerInterface $entityManager): Response
    {
        $publication = $publicationRepository->findWithCommentaires($id);

        if (!$publication) {
            throw $this->createNotFoundException('La publication n\'existe pas.');
        }

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('danger', 'Vous devez être connecté pour commenter.');
                return $this->redirectToRoute('app_login');
            }

            $commentaire->setAuteur($this->getUser());
            $commentaire->setPublication($publication);
            $commentaire->setDateCreation(new \DateTimeImmutable());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté !');
            return $this->redirectToRoute('app_forum_show', ['id' => $id]);
        }

        return $this->render('forum/show.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/modifier/{id}', name: 'app_forum_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, PublicationRepository $publicationRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $publication = $publicationRepository->find($id);

        if (!$publication) {
            throw $this->createNotFoundException('La publication n\'existe pas.');
        }

        if ($publication->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de cette publication.');
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('publications_directory'), $newFilename);
                    $publication->setImageFilename($newFilename);
                } catch (\Exception $e) {}
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre publication a été mise à jour !');
            return $this->redirectToRoute('app_forum_show', ['id' => $id]);
        }

        return $this->render('forum/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            if ($publication->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de cette publication.');
            }
            
            $entityManager->remove($publication);
            $entityManager->flush();
            $this->addFlash('success', 'Publication supprimée.');
        }

        return $this->redirectToRoute('app_forum');
    }
}
