<?php
use App\Entity\User;
use App\Entity\Profile;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$kernel = new App\Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();
$container = $kernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();
$passwordHasher = $container->get('security.password_hasher');

$userRepo = $entityManager->getRepository(User::class);
$admin = $userRepo->findOneBy(['email' => 'admin@firmetna.com']);

if (!$admin) {
    $admin = new User();
    $admin->setEmail('admin@firmetna.com');
    $admin->setRole('ROLE_ADMIN');
    $admin->setPassword($passwordHasher->hashPassword($admin, 'admin123'));

    $profile = new Profile();
    $profile->setNom('Admin');
    $profile->setPrenom('Firmetna');
    $profile->setRoleType('Agriculteur');
    $profile->setStatut('Actif');
    $profile->setUser($admin);

    $entityManager->persist($admin);
    $entityManager->persist($profile);
    $entityManager->flush();
    echo "Admin user created successfully!\n";
} else {
    // Ensure it has ROLE_ADMIN
    $admin->setRole('ROLE_ADMIN');
    $entityManager->flush();
    echo "Admin user already exists. ROLE_ADMIN ensured.\n";
}
