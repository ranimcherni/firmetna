<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config/bootstrap.php';

use App\Kernel;
use App\Entity\User;

$kernel = new Kernel('dev', true);
$kernel->boot();
$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

$email = 'angelina.jolie@gmail.com';
$user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

if ($user) {
    echo "--- USER FOUND ---\n";
    echo "Email: " . $user->getEmail() . "\n";
    echo "Nom: " . $user->getNom() . "\n";
    echo "Prenom: " . $user->getPrenom() . "\n";
    echo "Role: " . $user->getRole() . "\n";
    echo "RoleType: " . $user->getRoleType() . "\n";
    echo "Status: " . $user->getStatut() . "\n";
    echo "Date Inscription: " . ($user->getDateInscription() ? $user->getDateInscription()->format('Y-m-d H:i:s') : 'N/A') . "\n";
} else {
    echo "--- USER NOT FOUND ---\n";
    
    // Check all users
    $allUsers = $em->getRepository(User::class)->findAll();
    echo "Total users in DB: " . count($allUsers) . "\n";
    foreach ($allUsers as $u) {
        echo "- " . $u->getEmail() . " (" . $u->getRole() . ")\n";
    }
}
