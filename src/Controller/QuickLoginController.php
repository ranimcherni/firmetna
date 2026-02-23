<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class QuickLoginController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/quick-login', name: 'app_quick_login')]
    public function quickLogin(Request $request): Response
    {
        // Get admin user
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@firmetna.com']);
        
        if ($user) {
            // Create authentication token
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            
            // Set token in security context
            $this->container->get('security.token_storage')->setToken($token);
            
            // Set user in session
            $session = $request->getSession();
            $session->set('_security_main', serialize($token));
            
            $this->addFlash('success', 'Connecté en tant qu\'administrateur!');
        }
        
        // Redirect to front dashboard
        return $this->redirectToRoute('app_front_dashboard');
    }
}
