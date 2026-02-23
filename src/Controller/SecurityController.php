<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // TEMPORARY BYPASS FOR PRESENTATION - REMOVE AFTER!
        if (isset($_GET['bypass']) && $_GET['bypass'] === 'admin123') {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@firmetna.com']);
            if ($user) {
                // Just redirect directly to admin dashboard - skip authentication
                return $this->redirectToRoute('app_admin_dashboard');
            }
        }

        // Si l'utilisateur est déjà connecté, redirige vers la page d'accueil
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin_dashboard');
            }
            return $this->redirectToRoute('app_front_dashboard');
        }

        // Récupérer l'erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Dernier email entré par l'utilisateur
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastEmail,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Cette méthode peut être vide - elle sera interceptée par le firewall
        throw new \LogicException('Cette méthode sera interceptée par le firewall de déconnexion.');
    }
}