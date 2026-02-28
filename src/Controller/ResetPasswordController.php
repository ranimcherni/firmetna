<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            
            /** @var User $user */
            $user = $this->userRepository->findOneBy(['email' => $email]);

            // Ne pas révéler si l'email existe ou non pour des raisons de sécurité
            // Mais pour l'UX, on redirige vers la vérification si on trouve l'utilisateur
            if ($user) {
                try {
                    // Générer un code à 6 chiffres
                    $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    // Stocker le code en session
                    $request->getSession()->set('reset_code', $resetCode);
                    $request->getSession()->set('reset_email', $email);
                    $request->getSession()->set('reset_expires', (new \DateTime('+1 hour'))->getTimestamp());
                    
                    $emailMessage = (new Email())
                        ->from('ryhemoueslati00@gmail.com')
                        ->to($user->getEmail())
                        ->subject('🔑 Votre code de réinitialisation FIRMETNA')
                        ->html($this->renderView('reset_password/email.html.twig', [
                            'resetCode' => $resetCode,
                            'user' => $user
                        ]));

                    $this->mailer->send($emailMessage);

                    return $this->redirectToRoute('app_forgot_password_verify');
                } catch (\Exception $e) {
                    error_log('MAILER ERROR: ' . $e->getMessage());
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email : ' . $e->getMessage());
                }
            } else {
                // Même si l'utilisateur n'existe pas, on peut rediriger vers une page "vérifiez vos mails"
                // ou rester ici avec un message générique.
                $this->addFlash('info', 'Si un compte existe avec cet email, un code de vérification a été envoyé.');
                return $this->redirectToRoute('app_forgot_password_request');
            }
        }

        return $this->render('reset_password/request.html.twig');
    }

    #[Route('/verify', name: 'app_forgot_password_verify')]
    public function verify(Request $request): Response
    {
        $session = $request->getSession();
        $storedCode = $session->get('reset_code');
        $storedEmail = $session->get('reset_email');
        $expires = $session->get('reset_expires');

        if (!$storedCode || !$storedEmail || time() > $expires) {
            $this->addFlash('error', 'Votre session a expiré. Veuillez recommencer.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        if ($request->isMethod('POST')) {
            $codeChunks = $request->request->all('code'); // Si on utilise plusieurs inputs
            $submittedCode = is_array($codeChunks) ? implode('', $codeChunks) : $request->request->get('code');

            if ($submittedCode === $storedCode) {
                $session->set('reset_verified', true);
                return $this->redirectToRoute('app_forgot_password_reset');
            }

            $this->addFlash('error', 'Le code de vérification est incorrect.');
        }

        return $this->render('reset_password/verify.html.twig', [
            'email' => $storedEmail
        ]);
    }

    #[Route('/reset', name: 'app_forgot_password_reset')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $session = $request->getSession();
        if (!$session->get('reset_verified')) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $email = $session->get('reset_email');
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
                $this->userRepository->save($user, true);

                $session->remove('reset_code');
                $session->remove('reset_email');
                $session->remove('reset_expires');
                $session->remove('reset_verified');

                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('reset_password/reset.html.twig');
    }
}
