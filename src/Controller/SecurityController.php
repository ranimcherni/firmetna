<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // ... (rest of the code)
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin_dashboard');
            }
            return $this->redirectToRoute('app_front_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastEmail,
            'error' => $error,
        ]);
    }

    #[Route('/verify-face', name: 'app_verify_face', methods: ['POST'])]
    public function verifyFace(Request $request, UserRepository $userRepository, Security $security): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['email'] ?? '';
            $liveSignature = $data['signature'] ?? null;

            if (!$email || !$liveSignature || !is_array($liveSignature)) {
                error_log("Face Verify: Invalid data for email: " . ($email ?: 'empty'));
                return new JsonResponse(['success' => false, 'message' => 'Données de signature invalides ou manquantes'], 400);
            }

            $user = $userRepository->findOneBy(['email' => $email]);
            if (!$user) {
                error_log("Face Verify: User not found: " . $email);
                return new JsonResponse(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
            }

            if ($user->getStatut() === 'Inactif') {
                error_log("Face Verify: Account blocked for: " . $email);
                return new JsonResponse(['success' => false, 'message' => 'Désolé, votre compte est bloqué.'], 403);
            }

            if (!$user->getFaceSignature()) {
                error_log("Face Verify: Face recognition not enabled for: " . $email);
                return new JsonResponse(['success' => false, 'message' => 'Reconnaissance faciale non activée sur ce compte'], 400);
            }

            $storedSignature = json_decode($user->getFaceSignature(), true);
            if (!$storedSignature || !is_array($storedSignature)) {
                error_log("Face Verify: Corrupted stored signature for: " . $email);
                return new JsonResponse(['success' => false, 'message' => 'Signature stockée corrompue'], 500);
            }

            $distance = $this->calculateEuclideanDistance($liveSignature, $storedSignature);
            
            error_log(sprintf("Face Verify Result - Email: %s, Distance: %.4f, Threshold: 0.50", $email, $distance));

            if ($distance <= 0.50) {
                // Log user in
                $security->login($user, 'form_login', 'main');
                
                // Determine redirect URL
                $redirectUrl = $this->generateUrl('app_front_dashboard');
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    $redirectUrl = $this->generateUrl('app_admin_dashboard');
                }

                return new JsonResponse([
                    'success' => true, 
                    'distance' => $distance,
                    'redirect' => $redirectUrl
                ]);
            }

            return new JsonResponse([
                'success' => false, 
                'message' => "Visage non reconnu", 
                'distance' => $distance
            ]);
        } catch (\Exception $e) {
            error_log("Face Verify EXCEPTION: " . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }

    private function calculateEuclideanDistance(array $sig1, array $sig2): float
    {
        if (count($sig1) !== count($sig2)) {
            return 1.0;
        }

        $sum = 0;
        for ($i = 0; $i < count($sig1); $i++) {
            $sum += pow($sig1[$i] - $sig2[$i], 2);
        }

        return sqrt($sum);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Cette m├®thode peut ├¬tre vide - elle sera intercept├®e par le firewall
        throw new \LogicException('Cette m├®thode sera intercept├®e par le firewall de d├®connexion.');
    }
}
