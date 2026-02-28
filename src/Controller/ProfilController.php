<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profil')]
final class ProfilController extends AbstractController
{
    #[Route(name: 'app_profil_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('profil/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/me', name: 'app_profil_me', methods: ['GET'])]
    public function me(PublicationRepository $publicationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les statistiques de l'utilisateur
        $publicationsCount = $publicationRepository->count(['auteur' => $user]);
        $recentPublications = $publicationRepository->findBy(
            ['auteur' => $user],
            ['dateCreation' => 'DESC'],
            5
        );

        return $this->render('profil/show.html.twig', [
            'user' => $user,
            'publicationsCount' => $publicationsCount,
            'recentPublications' => $recentPublications,
        ]);
    }

    #[Route('/new', name: 'app_profil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profil_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('profil/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Security: Can only edit yourself
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce profil.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_profil_me', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Security: Can only delete yourself
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce compte.');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            
            // Invalidate session and token
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
