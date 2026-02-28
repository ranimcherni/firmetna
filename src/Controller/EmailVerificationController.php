<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Entity\EmailVerification;
use App\Form\CommandeOrderType;
use App\Service\CartService;
use App\Service\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

#[Route('/email-verification')]
class EmailVerificationController extends AbstractController
{
    private EmailVerificationService $emailService;
    private Session $session;
    private LoggerInterface $logger;

    public function __construct(
        EmailVerificationService $emailService,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->emailService = $emailService;
        $this->session = $requestStack->getSession();
        $this->logger = $logger;
    }

    #[Route('/send', name: 'app_email_send', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function sendCode(Request $request): Response
    {
        $allData = $request->request->all();
        $formData = $allData['commande_order'] ?? [];

        $email = $formData['email'] ?? null;
        $productId = $allData['productId'] ?? null;
        $quantity = $formData['quantite'] ?? null;
        $adresse = $formData['adresseLivraison'] ?? null;
        $commentaire = $formData['commentaire'] ?? null;

        error_log('Email Debug - Received data: ' . json_encode([
            'email' => $email,
            'productId' => $productId,
            'quantity' => $quantity,
            'adresse' => $adresse,
            'commentaire' => $commentaire,
            'all_request_data' => $allData,
            'form_data' => $formData
        ], true));

        if (!$email || !$productId || !$quantity) {
            $this->addFlash('error', 'Informations manquantes pour la vérification par email.');
            return $this->redirectToRoute('app_products_order', ['id' => $productId]);
        }

        try {
            $verification = $this->emailService->sendVerificationCode($email);

            $this->session->set('pending_order', [
                'productId' => $productId,
                'quantity' => $quantity,
                'email' => $email,
                'adresse' => $adresse,
                'commentaire' => $commentaire,
                'verification_id' => $verification->getId()
            ]);

            $this->addFlash('success', 'Code de vérification envoyé par email. Veuillez vérifier votre boîte de réception.');
            
            return $this->redirectToRoute('app_email_verify');

        } catch (\Exception $e) {
            error_log('Email Error: ' . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage() . '. Veuillez réessayer.');
            return $this->redirectToRoute('app_products_order', ['id' => $productId]);
        }
    }

    #[Route('/verify', name: 'app_email_verify')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function verifyCode(Request $request): Response
    {
        return $this->render('front/products/email_verification.html.twig');
    }

    #[Route('/check', name: 'app_email_check', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function checkCode(Request $request, EntityManagerInterface $entityManager): Response
    {
        $code = $request->request->get('code');
        $pendingOrder = $this->session->get('pending_order');
        
        if (!$pendingOrder) {
            return $this->json(['success' => false, 'message' => 'Aucune commande en attente.']);
        }

        $email = $pendingOrder['email'];

        try {
            $verification = $this->emailService->verifyCode($email, $code);

            if ($verification) {
                $this->session->set('verified_order', [
                    'productId' => $pendingOrder['productId'],
                    'quantity' => $pendingOrder['quantity'],
                    'email' => $email,
                    'adresse' => $pendingOrder['adresse'],
                    'commentaire' => $pendingOrder['commentaire'],
                    'verification_id' => $verification->getId()
                ]);

                return $this->json([
                    'success' => true, 
                    'message' => 'Code vérifié! Veuillez confirmer la commande.',
                    'redirect' => $this->generateUrl('app_email_confirm')
                ]);
            }

            return $this->json(['success' => false, 'message' => 'Code invalide ou expiré.']);

        } catch (\Exception $e) {
            $this->logger->error('Verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $email,
                'code' => $code
            ]);
            
            return $this->json(['success' => false, 'message' => 'Erreur lors de la vérification.']);
        }
    }

    #[Route('/confirm', name: 'app_email_confirm', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function confirmOrder(Request $request, EntityManagerInterface $entityManager): Response
    {
        $verifiedOrder = $this->session->get('verified_order');
        
        if (!$verifiedOrder) {
            return $this->json(['success' => false, 'message' => 'Aucune commande vérifiée en attente.']);
        }

        try {
            $product = $entityManager->getRepository(Produit::class)->find($verifiedOrder['productId']);
            $user = $this->getUser();

            $commande = new Commande();
            $commande->setDateCommande(new \DateTimeImmutable());
            $commande->setStatut('Confirmée');
            $commande->setAdresseLivraison($verifiedOrder['adresse']);
            $commande->setTotal((float) ($product->getPrix() * $verifiedOrder['quantity']));
            $commande->setCommentaire($verifiedOrder['commentaire']);
            $commande->setClient($user);
            $commande->setEmail($verifiedOrder['email']);
            $entityManager->persist($commande);

            $ligneCommande = new LigneCommande();
            $ligneCommande->setQuantite($verifiedOrder['quantity']);
            $ligneCommande->setPrixUnitaire($product->getPrix());
            $ligneCommande->setSousTotal($product->getPrix() * $verifiedOrder['quantity']);
            $ligneCommande->setCommande($commande);
            $ligneCommande->setProduit($product);
            $entityManager->persist($ligneCommande);

            $verification = $entityManager->getRepository(EmailVerification::class)->find($verifiedOrder['verification_id']);
            if ($verification) {
                $verification->setCommande($commande);
                $entityManager->persist($verification);
            }

            $entityManager->flush();

            $this->emailService->sendOrderConfirmation($verifiedOrder['email'], [
                'id' => $commande->getId(),
                'product_name' => $product->getNom(),
                'quantity' => $verifiedOrder['quantity'],
                'total' => $commande->getTotal()
            ]);

            $this->session->remove('pending_order');
            $this->session->remove('verified_order');

            return $this->json([
                'success' => true, 
                'message' => 'Commande confirmée avec succès!',
                'redirect' => $this->generateUrl('app_products_order_success', ['id' => $commande->getId()])
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Order confirmation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->json(['success' => false, 'message' => 'Erreur lors de la confirmation de la commande.']);
        }
    }

    #[Route('/resend', name: 'app_email_resend', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function resendCode(): Response
    {
        $pendingOrder = $this->session->get('pending_order');
        
        if (!$pendingOrder) {
            return $this->json(['success' => false, 'message' => 'Aucune commande en attente.']);
        }

        try {
            $verification = $this->emailService->sendVerificationCode($pendingOrder['email']);
            
            $pendingOrder['verification_id'] = $verification->getId();
            $this->session->set('pending_order', $pendingOrder);

            return $this->json(['success' => true, 'message' => 'Code renvoyé avec succès.']);

        } catch (\Exception $e) {
            $this->logger->error('Resend failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->json(['success' => false, 'message' => 'Erreur lors du renvoi du code.']);
        }
    }
}
