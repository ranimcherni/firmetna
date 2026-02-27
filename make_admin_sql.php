<?php
require __DIR__ . '/vendor/autoload.php';
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', true);
$kernel->boot();

$conn = $kernel->getContainer()->get('doctrine')->getConnection();
$sql = "UPDATE user SET roles = '[\"ROLE_ADMIN\"]' WHERE email LIKE '%yessine%' OR prenom LIKE '%yessine%'";
$stmt = $conn->prepare($sql);
$stmt->execute();

echo "User updated successfully to ROLE_ADMIN!\n";
