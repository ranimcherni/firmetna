<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Mailer\Transport\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

try {
    $dsn = 'smtp://cc4672b3fa53c9:fada59127e8385@sandbox.smtp.mailtrap.io:2525';
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);
    
    $email = (new Email())
        ->from('test@firmetna.com')
        ->to('test@example.com')
        ->subject('Test Email from FIRMETNA')
        ->text('This is a test email from FIRMETNA - Mailtrap is working!');
    
    $result = $mailer->send($email);
    echo "SUCCESS: Email sent to Mailtrap!\n";
    echo "Message ID: " . $result->getMessageId() . "\n";
    echo "Check your Mailtrap inbox to see the email.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
