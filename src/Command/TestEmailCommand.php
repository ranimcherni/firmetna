<?php

namespace App\Command;

use App\Service\PhpMailerEventService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-email',
    description: 'Send a test email via PhpMailerEventService to verify SMTP configuration',
)]
class TestEmailCommand extends Command
{
    public function __construct(private PhpMailerEventService $mailer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('to', InputArgument::REQUIRED, 'Recipient email address');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $to = $input->getArgument('to');

        $html = <<<HTML
<!DOCTYPE html>
<html>
<body>
  <h2 style="color:#1a4d2e;">Test Email — FIRMETNA</h2>
  <p>Ceci est un email de test pour vérifier la configuration SMTP de l'application.</p>
  <p>Si vous recevez cet email, l'envoi fonctionne correctement ✅</p>
  <hr>
  <small>Envoyé depuis PhpMailerEventService — {$_ENV['APP_ENV']} environment</small>
</body>
</html>
HTML;

        $io->info("Envoi d'un email de test à : $to");

        $success = $this->mailer->sendHtmlEmail(
            $to,
            '✅ Test Email — FIRMETNA',
            $html
        );

        if ($success) {
            $io->success("Email envoyé avec succès à $to !");
            return Command::SUCCESS;
        } else {
            $io->error("Échec de l'envoi. Vérifiez les logs et la configuration SMTP dans .env");
            return Command::FAILURE;
        }
    }
}
