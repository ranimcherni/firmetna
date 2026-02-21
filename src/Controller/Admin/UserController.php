<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserAdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $q = $request->query->get('q');
        $type = $request->query->get('type');
        $statut = $request->query->get('statut');

        $queryBuilder = $userRepository->createQueryBuilder('u');

        if ($q) {
            if (is_numeric($q)) {
                $queryBuilder->andWhere('u.id = :id')
                    ->setParameter('id', $q);
            } else {
                $queryBuilder->andWhere('u.email LIKE :q OR u.nom LIKE :q OR u.prenom LIKE :q')
                    ->setParameter('q', '%'.$q.'%');
            }
        }

        if ($type && $type !== 'Tous les types') {
            $queryBuilder->andWhere('u.roleType = :type')
                ->setParameter('type', $type);
        }

        if ($statut && $statut !== 'Tous les statuts') {
            $queryBuilder->andWhere('u.statut = :statut')
                ->setParameter('statut', $statut);
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $queryBuilder->getQuery()->getResult(),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserAdminType::class, $user, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setDateInscription(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur cr├®├® avec succ├¿s !');
            return $this->redirectToRoute('app_admin_user_index');
        }

        // Display validation errors
        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserAdminType::class, $user, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifi├® !');
            return $this->redirectToRoute('app_admin_user_index');
        }

        // Display validation errors
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprim├® !');
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}
