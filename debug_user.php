<?php
require_once 'vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

// Test database connection and user retrieval
$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/config'));
$loader->load('packages/doctrine.yaml');

// Get database connection
$connection = $container->get('doctrine.dbal.default_connection');

// Query user
$stmt = $connection->executeQuery('SELECT * FROM user WHERE email = ?', ['admin@firmetna.com']);
$user = $stmt->fetchAssociative();

if ($user) {
    echo "User found:\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Password hash: " . $user['password'] . "\n";
} else {
    echo "User not found!\n";
}
?>
