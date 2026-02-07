<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:setup-admin',
    description: 'Creates or ensures the admin user exists.',
)]
class SetupAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userRepo = $this->entityManager->getRepository(User::class);
        $admin = $userRepo->findOneBy(['email' => 'admin@firmetna.com']);

        if (!$admin) {
            $admin = new User();
            $admin->setEmail('admin@firmetna.com');
            $admin->setRole('ROLE_ADMIN');
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

            $admin->setNom('Admin');
            $admin->setPrenom('Firmetna');
            $admin->setRoleType('Agriculteur');
            $admin->setStatut('Actif');

            $this->entityManager->persist($admin);
            $this->entityManager->flush();
            $io->success('Admin user created successfully!');
        } else {
            $admin->setRole('ROLE_ADMIN');
            $this->entityManager->flush();
            $io->info('Admin user already exists. ROLE_ADMIN ensured.');
        }

        return Command::SUCCESS;
    }
}
