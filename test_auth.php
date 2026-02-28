<?php
require_once 'vendor/autoload.php';

use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

// Test password hashing
$hasher = new NativePasswordHasher();
$password = 'admin123';
$hash = $hasher->hash($password);

echo "Generated hash: " . $hash . "\n";
echo "Password verification: " . ($hasher->verify($hash, $password) ? 'SUCCESS' : 'FAILED') . "\n";

// Test with existing hash from database
$existingHash = '$2y$10$Ag6UYyrCEChg0.i6GvlcXeKKr8AwxkHa9F6jPsKISFUa0sJm.46dS';
echo "Existing hash verification: " . ($hasher->verify($existingHash, $password) ? 'SUCCESS' : 'FAILED') . "\n";
?>
