<?php

use App\Kernel;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

require dirname(__DIR__).'/firmetna/vendor/autoload.php';

$kernel = new Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine.orm.entity_manager');
$passwordHasher = $container->get('parameter_bag')->get('kernel.environment') === 'test' ? null : $container->get('security.user_password_hasher');

$email = 'admin@firmetna.com';
$password = 'admin123';

$userRepository = $entityManager->getRepository(User::class);
$user = $userRepository->findOneBy(['email' => $email]);

if (!$user) {
    $user = new User();
    $user->setEmail($email);
    $user->setNom('Admin');
    $user->setPrenom('Firmetna');
    $user->setRole('ROLE_ADMIN');
    $user->setStatut('Actif');
    $user->setRoleType('Administrateur');
    $user->setDateInscription(new \DateTime());
    
    $hashedPassword = $passwordHasher->hashPassword($user, $password);
    $user->setPassword($hashedPassword);
    
    $entityManager->persist($user);
    $entityManager->flush();
    
    echo "Admin user created successfully!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
} else {
    echo "User $email already exists.\n";
    // Promote to admin if not already
    if ($user->getRole() !== 'ROLE_ADMIN') {
        $user->setRole('ROLE_ADMIN');
        $entityManager->flush();
        echo "User promoted to ROLE_ADMIN.\n";
    }
}
