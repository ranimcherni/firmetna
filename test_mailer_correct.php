<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

try {
    // Test with the correct DSN format from Mailtrap
    $dsn = 'smtp://cc4672b3fa53c9:fada59127e8385@sandbox.smtp.mailtrap.io:2525';
    $transport = Transport::fromDsn($dsn);
    
    $mailer = new Mailer($transport);
    
    $email = (new Email())
        ->from('test@firmetna.com')
        ->to('houridhia1@gmail.com')
        ->subject('Test Email from FIRMETNA - Correct DSN!')
        ->text('This is a test email from FIRMETNA - Mailtrap DSN should work now!');
    
    $result = $mailer->send($email);
    echo "SUCCESS: Email sent to Mailtrap!\n";
    echo "DSN used: " . $dsn . "\n";
    echo "Check your Mailtrap inbox to see the email.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "DSN used: " . $dsn . "\n";
}
