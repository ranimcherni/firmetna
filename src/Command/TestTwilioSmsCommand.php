<?php

namespace App\Command;

use App\Service\TwilioService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-sms',
    description: 'Test sending an SMS via Twilio',
)]
class TestTwilioSmsCommand extends Command
{
    private TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        parent::__construct();
        $this->twilioService = $twilioService;
    }

    protected function configure(): void
    {
        $this->addArgument('phone', InputArgument::REQUIRED, 'The destination phone number (e.g. 55555555)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $phone = (string) $input->getArgument('phone');

        $io->title('Testing Twilio SMS Service');
        $io->text('Sending SMS to: ' . $phone);

        try {
            $message = 'Test SMS from firmetna. The Twilio integration is working correctly!';
            $this->twilioService->sendSms($phone, $message);
            $io->success('SMS sent successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Failed to send SMS: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
