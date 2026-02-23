<?php
require_once 'vendor/autoload.php';

use App\Service\EmailVerificationService;
use Psr\Log\LoggerInterface;
use App\Repository\EmailVerificationRepository;
use Symfony\Component\Mailer\MailerInterface;

// Mock dependencies for testing
$logger = new class implements LoggerInterface {
    public function emergency($message, array $context = []): void {}
    public function alert($message, array $context = []): void {}
    public function critical($message, array $context = []): void {}
    public function error($message, array $context = []): void {}
    public function warning($message, array $context = []): void {}
    public function notice($message, array $context = []): void {}
    public function info($message, array $context = []): void {}
    public function debug($message, array $context = []): void {}
    public function log($level, $message, array $context = []): void {}
};

$repository = new class {
    public function save($entity, $flush = false) {
        echo "Mock: Saving verification code to database\n";
    }
    public function findValidByEmail($email) {
        echo "Mock: Finding valid code for email: $email\n";
        return null;
    }
    public function cleanupExpiredCodes($email) {
        echo "Mock: Cleaning up expired codes for email: $email\n";
    }
};

// Create a simple mailer test
$dsn = 'smtp://cc4672b3fa53c9:fada59127e8385@sandbox.smtp.mailtrap.io:2525';
$transport = \Symfony\Component\Mailer\Transport::fromDsn($dsn);
$mailer = new \Symfony\Component\Mailer\Mailer($transport);

$emailService = new EmailVerificationService($logger, $repository, $mailer);

echo "Testing EmailVerificationService...\n";
try {
    $verification = $emailService->sendVerificationCode('houridhia1@gmail.com');
    echo "SUCCESS: Verification code sent!\n";
    echo "Code: " . $verification->getCode() . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
