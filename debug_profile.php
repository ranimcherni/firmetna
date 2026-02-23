<?php
require_once 'vendor/autoload.php';

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

$entityManager = new \Doctrine\ORM\EntityManager(
    new \Doctrine\DBAL\Connection(['driver' => 'pdo_mysql', 'host' => '127.0.0.1', 'port' => '3307', 'dbname' => 'firmetna_new_db', 'user' => 'root', 'password' => '']),
    new \Doctrine\ORM\Configuration()
);

$user = $entityManager->find(User::class, 21); // ID 21 = ranimcherni03@gmail.com

echo "=== DEBUG PROFIL ===\n";
echo "ID: " . $user->getId() . "\n";
echo "Email: " . $user->getEmail() . "\n";
echo "Role: " . $user->getRole() . "\n";
echo "RoleType: " . $user->getRoleType() . "\n";
echo "Prénom: " . $user->getPrenom() . "\n";
echo "Nom: " . . $user->getNom() . "\n";
echo "Téléphone: " . . $user->getTelephone() . "\n";

// Test de modification
$user->setRoleType('Agriculteur');
$entityManager->flush();

echo "\n=== APRÈS MODIFICATION ===\n";
echo "Nouveau RoleType: " . $user->getRoleType() . "\n";
