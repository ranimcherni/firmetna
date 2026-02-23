<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config/bootstrap.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();
$em = $container->get('doctrine.orm.entity_manager');

$meta = $em->getClassMetadata(\App\Entity\User::class);
echo "Fields in User entity:\n";
foreach ($meta->getFieldNames() as $fieldName) {
    echo "- $fieldName\n";
}

$conn = $em->getConnection();
$tables = $conn->createSchemaManager()->listTableNames();
echo "\nTables in database:\n";
foreach ($tables as $table) {
    echo "- $table\n";
}
