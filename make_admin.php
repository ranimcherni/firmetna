<?php

require __DIR__ . '/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();
$userRepository = $entityManager->getRepository(\App\Entity\User::class);

// Rechercher l'utilisateur 'yessine' (ou par email s'il en a un spécifique, mais on va d'abord chercher par prenom ou nom)
$users = $userRepository->findAll();
$found = false;

foreach ($users as $u) {
    if (stripos($u->getPrenom(), 'yessine') !== false || stripos($u->getNom(), 'yessine') !== false || stripos($u->getEmail(), 'yessine') !== false) {
        $roles = $u->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $u->setRoles($roles);
            $entityManager->flush();
            echo "✅ Succès : L'utilisateur " . $u->getEmail() . " (" . $u->getPrenom() . ") a maintenant le rôle ROLE_ADMIN !\n";
        } else {
            echo "ℹ️ L'utilisateur " . $u->getEmail() . " a DÉJÀ le rôle ROLE_ADMIN.\n";
        }
        $found = true;
        break;
    }
}

if (!$found) {
    echo "❌ Erreur : Impossible de trouver un utilisateur nommé 'yessine'.\n";
}
