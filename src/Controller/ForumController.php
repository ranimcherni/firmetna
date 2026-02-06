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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/forum')]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum', methods: ['GET'])]
    public function index(PublicationRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $repo->createQueryBuilder('p')
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery();

        $publications = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('forum/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    #[Route('/nouvelle', name: 'app_forum_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $publication = new Publication();
        $publication->setDateCreation(new \DateTimeImmutable());
        $publication->setAuteur($this->getUser());

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/publications';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $imageFile->move($uploadDir, $newFilename);
                    $publication->setImageFilename('/uploads/publications/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $em->persist($publication);
            $em->flush();
            $this->addFlash('success', 'Votre publication a été créée.');
            return $this->redirectToRoute('app_forum_show', ['id' => $publication->getId()]);
        }

        return $this->render('forum/new.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(int $id, PublicationRepository $repo, Request $request, EntityManagerInterface $em): Response
    {
        $publication = $repo->findWithCommentaires($id);
        if (!$publication) {
            throw $this->createNotFoundException('Publication introuvable.');
        }

        $commentaire = new Commentaire();
        $commentaire->setPublication($publication);
        $commentaire->setAuteur($this->getUser());
        $commentaire->setDateCreation(new \DateTimeImmutable());

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commentaire);
            $em->flush();
            $this->addFlash('success', 'Votre commentaire a été ajouté.');
            return $this->redirectToRoute('app_forum_show', ['id' => $id]);
        }

        return $this->render('forum/show.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_forum_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Publication $publication, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if ($publication->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres publications.');
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $oldFilename = $publication->getImageFilename();
                if ($oldFilename) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public' . $oldFilename;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/publications';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $imageFile->move($uploadDir, $newFilename);
                    $publication->setImageFilename('/uploads/publications/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $em->flush();
            $this->addFlash('success', 'Publication mise à jour.');
            return $this->redirectToRoute('app_forum_show', ['id' => $publication->getId()]);
        }

        return $this->render('forum/edit.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_forum_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $em): Response
    {
        if ($publication->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres publications.');
        }

        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $publication->getId(), $token)) {
            $imageFilename = $publication->getImageFilename();
            if ($imageFilename) {
                $imagePath = $this->getParameter('kernel.project_dir') . '/public' . $imageFilename;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            foreach ($publication->getCommentaires() as $commentaire) {
                $em->remove($commentaire);
            }
            $em->remove($publication);
            $em->flush();
            $this->addFlash('success', 'Publication et ses commentaires supprimés.');
        }

        return $this->redirectToRoute('app_forum');
    }
}
