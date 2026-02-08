<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config/bootstrap.php';
use App\Kernel;
$kernel = new Kernel('dev', true);
$kernel->boot();
$conn = $kernel->getContainer()->get('doctrine.dbal.default_connection');
echo "--- DATABASE INFO ---\n";
echo "Host: " . $conn->getHost() . "\n";
echo "Port: " . $conn->getPort() . "\n";
echo "Database: " . $conn->getDatabase() . "\n";
echo "User: " . $conn->getUsername() . "\n";
