<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:test-symfony-mailer',
    description: 'Test sending an email using Symfony Mailer (MAILER_DSN)',
)]
class TestSymfonyMailerCommand extends Command
{
    public function __construct(private MailerInterface $mailer)
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

        try {
            $email = (new Email())
                ->from('ryhemoueslati00@gmail.com')
                ->to($to)
                ->subject('Test Symfony Mailer DSN')
                ->text('Testing MAILER_DSN with Gmail...');

            $this->mailer->send($email);

            $io->success("Email envoyé avec succès via Symfony Mailer à $to !");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Erreur Symfony Mailer: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
