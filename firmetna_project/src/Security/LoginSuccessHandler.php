<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        
        // Vérifier si l'utilisateur a le rôle ROLE_ADMIN
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // Rediriger vers le back office pour les admins
            return new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
        }
        
        // Rediriger vers le front office pour les utilisateurs normaux
        return new RedirectResponse($this->urlGenerator->generate('app_front_dashboard'));
    }
}
