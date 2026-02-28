<?php

namespace App\Service;

use App\Entity\EmailVerification;
use App\Repository\EmailVerificationRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailVerificationService
{
    private LoggerInterface $logger;
    private EmailVerificationRepository $emailVerificationRepository;
    private MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, EmailVerificationRepository $emailVerificationRepository, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->emailVerificationRepository = $emailVerificationRepository;
        $this->mailer = $mailer;
    }

    public function sendVerificationCode(string $email): EmailVerification
    {
        $this->logger->info('Starting email verification process', ['email' => $email]);
        
        // Clean up expired codes first
        $this->emailVerificationRepository->cleanupExpired();

        // Check if there's a recent unverified code (within 1 minute)
        $recentCode = $this->emailVerificationRepository->findValidByEmail($email);
        if ($recentCode && $recentCode->getCreatedAt() > new \DateTimeImmutable('-1 minute')) {
            return $recentCode; // Return existing code if too recent
        }

        // Create new verification
        $verification = new EmailVerification();
        $verification->setEmail($email);
        $verification->setCode($this->generateRandomCode());
        $this->emailVerificationRepository->save($verification, true);

        $this->logger->info('Generated verification code', ['email' => $email, 'code' => $verification->getCode()]);
        
        $this->logger->info('Verification code saved to database', [
            'email' => $email,
            'code' => $verification->getCode(),
            'expires_at' => $verification->getExpiresAt()->format('Y-m-d H:i:s'),
            'method' => 'email'
        ]);

        // Send verification email
        try {
            $this->sendVerificationEmail($email, $verification->getCode());
            
            $this->logger->info('Email verification code sent', [
                'email' => $email,
                'code' => $verification->getCode(),
                'expires_at' => $verification->getExpiresAt()->format('Y-m-d H:i:s'),
                'method' => 'email'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send verification email', [
                'to' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $verification;
    }

    public function verifyCode(string $email, string $code): ?EmailVerification
    {
        $verification = $this->emailVerificationRepository->findValidByEmail($email);
        if ($verification && $verification->isValidCode($code)) {
            $verification->setVerified(true);
            $this->emailVerificationRepository->save($verification, true);

            $this->logger->info('Email verification successful', [
                'email' => $email,
                'code' => $code,
                'verified_at' => $verification->getVerifiedAt()->format('Y-m-d H:i:s')
            ]);

            return $verification;
        }

        $this->logger->warning('Email verification failed', [
            'error' => 'Invalid code',
            'email' => $email,
            'provided_code' => $code,
            'stored_code' => $verification ? $verification->getCode() : 'none'
        ]);

        return null;
    }

    public function sendOrderConfirmation(string $email, array $orderInfo): bool
    {
        $message = sprintf(
            "FIRMETNA: Commande #%d confirmée! %d article(s), Total: %.2f EUR. Merci pour votre confiance!",
            $orderInfo['id'],
            $orderInfo['items_count'] ?? 1,
            $orderInfo['total']
        );

        $email = (new TemplatedEmail())
            ->from(new Address('noreply@firmetna.com', 'FIRMETNA'))
            ->to(new Address($email))
            ->subject('Confirmation de votre commande')
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'orderInfo' => $orderInfo,
                'message' => $message
            ]);

        try {
            $this->mailer->send($email);
            
            $this->logger->info('Order confirmation email sent', [
                'to' => $email,
                'order_id' => $orderInfo['id'],
                'message' => $message
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send order confirmation email', [
                'to' => $email,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    private function sendVerificationEmail(string $email, string $code): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@firmetna.com', 'FIRMETNA'))
            ->to(new Address($email))
            ->subject('Code de vérification FIRMETNA')
            ->htmlTemplate('emails/verification.html.twig')
            ->context([
                'code' => $code,
                'user_email' => $email,
                'expires_in_minutes' => 10
            ]);

        try {
            $this->mailer->send($email);
            
            $this->logger->info('Verification email sent successfully', [
                'to' => $email,
                'code' => $code
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send verification email', [
                'to' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function generateRandomCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}
