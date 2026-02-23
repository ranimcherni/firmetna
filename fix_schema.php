<?php

$dsn = 'mysql:host=127.0.0.1;port=3307;dbname=firmetna_new_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding columns to user table...\n";
    
    try {
        $pdo->exec("ALTER TABLE user ADD face_signature LONGTEXT DEFAULT NULL");
        echo "Column face_signature added.\n";
    } catch (Exception $e) {
        echo "face_signature could not be added (maybe already exists): " . $e->getMessage() . "\n";
    }
    
    try {
        $pdo->exec("ALTER TABLE user ADD facial_recognition_enabled TINYINT(1) DEFAULT 0 NOT NULL");
        echo "Column facial_recognition_enabled added.\n";
    } catch (Exception $e) {
        echo "facial_recognition_enabled could not be added (maybe already exists): " . $e->getMessage() . "\n";
    }
    
    echo "Done.\n";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
