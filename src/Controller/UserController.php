<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        // Récupérer les paramètres de filtrage
        $search = $request->query->get('search');
        
        // Créer la requête avec join sur le profil
        $queryBuilder = $userRepository->createQueryBuilder('u')
            ->leftJoin('u.profile', 'p')
            ->addSelect('p');
        
        if ($search) {
            $queryBuilder->andWhere('p.nom LIKE :search OR p.prenom LIKE :search OR u.email LIKE :search')
                        ->setParameter('search', '%' . $search . '%');
        }
        
        $users = $queryBuilder->getQuery()->getResult();
        
        // Statistiques (basées sur le profil roleType ou user role)
        $stats = [
            'agriculteurs' => $userRepository->createQueryBuilder('u')
                ->join('u.profile', 'p')
                ->select('count(u.id)')
                ->where('p.roleType = :role')
                ->setParameter('role', 'AGRICULTEUR')
                ->getQuery()
                ->getSingleScalarResult(),
            'consommateurs' => $userRepository->createQueryBuilder('u')
                ->join('u.profile', 'p')
                ->select('count(u.id)')
                ->where('p.roleType = :role')
                ->setParameter('role', 'CLIENT')
                ->getQuery()
                ->getSingleScalarResult(),
            'admins' => $userRepository->count(['role' => 'ROLE_ADMIN']),
        ];
        
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        // Initialiser un profil vide
        $profile = new Profile();
        $user->setProfile($profile);
        $profile->setDateInscription(new \DateTime());
        $profile->setStatut('ACTIF');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            
            // Le rôle est déjà défini dans le formulaire ou par défaut
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès!');
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès!');
        }

        return $this->redirectToRoute('app_user_index');
    }
}