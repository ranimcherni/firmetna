<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\EmailVerification;
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

#[Route('/panier')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CartController extends AbstractController
{
    private CartService $cartService;
    private EmailVerificationService $emailService;
    private Session $session;
    private LoggerInterface $logger;

    public function __construct(
        CartService $cartService,
        EmailVerificationService $emailService,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->cartService = $cartService;
        $this->emailService = $emailService;
        $this->session = $requestStack->getSession();
        $this->logger = $logger;
    }

    #[Route('/', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('front/cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    #[Route('/add-product/{id}', name: 'cart_add')]
    public function add(int $id, CartService $cartService): Response
    {
        $cartService->add($id);
        $this->addFlash('success', 'Produit ajouté au panier !');
        
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/increase/{uniqueId}', name: 'cart_increase')]
    public function increase(string $uniqueId, CartService $cartService): Response
    {
        $cartService->increaseQuantity($uniqueId);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{uniqueId}', name: 'cart_remove')]
    public function remove(string $uniqueId, CartService $cartService): Response
    {
        $cartService->remove($uniqueId);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{uniqueId}', name: 'cart_delete')]
    public function delete(string $uniqueId, CartService $cartService): Response
    {
        $cartService->delete($uniqueId);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/valider', name: 'cart_checkout')]
    public function checkout(Request $request): Response
    {
        $items = $this->cartService->getFullCart();
        
        if (empty($items)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_products');
        }

        // Handle email verification form submission
        if ($request->isMethod('POST')) {
            $this->logger->info('Cart checkout POST request received', [
                'email' => $request->request->get('email'),
                'all_data' => $request->request->all()
            ]);
            
            $email = $request->request->get('email');
            
            if (!$email) {
                $this->addFlash('error', 'Veuillez entrer votre adresse email.');
                return $this->redirectToRoute('cart_checkout');
            }

            try {
                $this->logger->info('About to send verification code', ['email' => $email]);
                
                // Send verification code
                $verification = $this->emailService->sendVerificationCode($email);
                
                $this->logger->info('Verification code created', [
                    'email' => $email,
                    'code' => $verification->getCode(),
                    'id' => $verification->getId()
                ]);
                
                // Store cart data and verification info in session
                $this->session->set('cart_order', [
                    'items' => $items,
                    'total' => $this->cartService->getTotal(),
                    'email' => $email,
                    'verification_id' => $verification->getId()
                ]);

                $this->addFlash('success', 'Code de vérification envoyé à votre adresse email.');
                return $this->redirectToRoute('cart_verify');
                
            } catch (\Exception $e) {
                $this->logger->error('Email verification failed', [
                    'error' => $e->getMessage(),
                    'email' => $email
                ]);
                $this->addFlash('error', 'Erreur lors de l\'envoi du code: ' . $e->getMessage());
                return $this->redirectToRoute('cart_checkout');
            }
        }

        // Show checkout form
        return $this->render('front/cart/checkout.html.twig', [
            'items' => $items,
            'total' => $this->cartService->getTotal()
        ]);
    }

    #[Route('/verify', name: 'cart_verify')]
    public function verify(Request $request): Response
    {
        return $this->render('front/cart/verify.html.twig');
    }

    #[Route('/check-code', name: 'cart_check_code', methods: ['POST'])]
    public function checkCode(Request $request, EntityManagerInterface $em): Response
    {
        $code = $request->request->get('code');
        $cartOrder = $this->session->get('cart_order');
        
        if (!$cartOrder) {
            return $this->json(['success' => false, 'message' => 'Aucune commande en attente.']);
        }

        try {
            $verification = $this->emailService->verifyCode($cartOrder['email'], $code);

            if ($verification) {
                // Create order from cart items
                $commande = new Commande();
                $commande->setClient($this->getUser());
                $commande->setDateCommande(new \DateTimeImmutable());
                $commande->setStatut('Confirmée');
                $commande->setEmail($cartOrder['email']);
                $commande->setTotal($cartOrder['total']);
                $em->persist($commande);

                // Create order lines from cart items
                foreach ($cartOrder['items'] as $item) {
                    $produit = $item['produit'];
                    $quantity = $item['quantity'];

                    $ligneCommande = new LigneCommande();
                    $ligneCommande->setQuantite($quantity);
                    $ligneCommande->setPrixUnitaire($produit->getPrix());
                    $ligneCommande->setCommande($commande);
                    $ligneCommande->setProduit($produit);
                    $em->persist($ligneCommande);

                    // Decrement stock
                    $produit->setStock($produit->getStock() - $quantity);
                    $em->persist($produit);
                }

                // Link verification to order
                $verification->setCommande($commande);
                $em->persist($verification);

                $em->flush();

                // Send confirmation email
                $this->emailService->sendOrderConfirmation($cartOrder['email'], [
                    'id' => $commande->getId(),
                    'items_count' => count($cartOrder['items']),
                    'total' => $commande->getTotal()
                ]);

                // Clear cart and sessions
                $this->cartService->clear();
                $this->session->remove('cart_order');

                $this->addFlash('success', 'Commande confirmée avec succès!');
                return $this->json([
                    'success' => true, 
                    'message' => 'Commande confirmée avec succès!',
                    'redirect' => $this->generateUrl('app_front_dashboard')
                ]);
            }

            return $this->json(['success' => false, 'message' => 'Code invalide ou expiré.']);

        } catch (\Exception $e) {
            $this->logger->error('Order confirmation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->json(['success' => false, 'message' => 'Erreur lors de la confirmation de la commande.']);
        }
    }
}
