<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_admin_commentaire_index', methods: ['GET'])]
    public function index(Request $request, CommentaireRepository $commentaireRepository): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $commentaireRepository->createQueryBuilder('c')
            ->leftJoin('c.auteur', 'u')
            ->leftJoin('c.publication', 'p')
            ->addSelect('u', 'p')
            ->orderBy('c.dateCreation', 'DESC');

        if ($q) {
            $queryBuilder->andWhere('c.contenu LIKE :q OR u.nom LIKE :q OR u.prenom LIKE :q OR p.titre LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        return $this->render('admin/commentaire/index.html.twig', [
            'commentaires' => $queryBuilder->getQuery()->getResult(),
        ]);
    }

    #[Route('/new', name: 'app_admin_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $commentaire->setDateCreation(new \DateTimeImmutable());
        $form = $this->createForm(\App\Form\CommentaireType::class, $commentaire, ['is_admin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès !');
            return $this->redirectToRoute('app_admin_commentaire_index');
        }

        return $this->render('admin/commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(\App\Form\CommentaireType::class, $commentaire, ['is_admin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire modifié avec succès !');
            return $this->redirectToRoute('app_admin_commentaire_index');
        }

        return $this->render('admin/commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire supprimé !');
        }

        return $this->redirectToRoute('app_admin_commentaire_index');
    }
}
