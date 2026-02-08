<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est d├®j├á connect├®, redirige vers la page d'accueil
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin_dashboard');
            }
            return $this->redirectToRoute('app_front_dashboard');
        }

        // R├®cup├®rer l'erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Dernier email entr├® par l'utilisateur
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastEmail,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Cette m├®thode peut ├¬tre vide - elle sera intercept├®e par le firewall
        throw new \LogicException('Cette m├®thode sera intercept├®e par le firewall de d├®connexion.');
    }
}
