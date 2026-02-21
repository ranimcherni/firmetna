<?php
require __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=firmetna_new_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$email = 'admin@firmetna.com';
$plainPassword = 'admin123';
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

$pdo->prepare('DELETE FROM user WHERE email = ?')->execute([$email]);

$stmt = $pdo->prepare('INSERT INTO user (email, password, role, nom, prenom, date_inscription, statut, role_type) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)');
$stmt->execute([$email, $hashedPassword, 'ROLE_ADMIN', 'Admin', 'Firmetna', 'Actif', 'Agriculteur']);

echo "Compte admin cree !\n";
echo "Email: $email\n";
echo "Mot de passe: $plainPassword\n";
