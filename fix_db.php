<?php
/**
 * Script to drop + recreate firmetna_new_db and build schema from Doctrine entities.
 */

// 1. Drop and recreate database via raw PDO
$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Step 1: Dropping database firmetna_new_db ===\n";
try {
    $pdo->exec('DROP DATABASE IF EXISTS firmetna_new_db');
    echo "Database dropped successfully.\n";
} catch (PDOException $e) {
    echo "Drop failed: " . $e->getMessage() . "\n";
    echo "Trying to clean up tablespace files manually...\n";
    
    // If DROP fails due to orphan .ibd files, try to remove them
    $dataDir = $pdo->query("SELECT @@datadir")->fetchColumn();
    $dbDir = $dataDir . 'firmetna_new_db';
    
    if (is_dir($dbDir)) {
        echo "Cleaning directory: $dbDir\n";
        $files = glob($dbDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                echo "  Deleted: " . basename($file) . "\n";
            }
        }
        // Try rmdir
        if (@rmdir($dbDir)) {
            echo "Directory removed.\n";
        }
    }
    
    // Retry drop
    try {
        $pdo->exec('DROP DATABASE IF EXISTS firmetna_new_db');
        echo "Database dropped after cleanup.\n";
    } catch (PDOException $e2) {
        echo "Still failed: " . $e2->getMessage() . "\n";
        echo "Please manually delete: $dbDir\n";
        exit(1);
    }
}

echo "\n=== Step 2: Creating database firmetna_new_db ===\n";
$pdo->exec('CREATE DATABASE firmetna_new_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
echo "Database created successfully.\n";

echo "\n=== Done! Now run: php bin/console doctrine:schema:update --force ===\n";
