<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

try {
    // Test with the corrected DSN format
    $dsn = 'smtp://smtp.mailtrap.io:2525';
    $transport = Transport::fromDsn($dsn);
    $transport->setUsername('cc4672b3fa53c9');
    $transport->setPassword('fada59127e8385');
    
    $mailer = new Mailer($transport);
    
    $email = (new Email())
        ->from('test@firmetna.com')
        ->to('houridhia1@gmail.com')
        ->subject('Test Email from FIRMETNA - Fixed!')
        ->text('This is a test email from FIRMETNA - Mailtrap should work now!');
    
    $result = $mailer->send($email);
    echo "SUCCESS: Email sent to Mailtrap!\n";
    echo "Message ID: " . $result->getMessageId() . "\n";
    echo "Check your Mailtrap inbox to see the email.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "DSN used: smtp://smtp.mailtrap.io:2525\n";
}
