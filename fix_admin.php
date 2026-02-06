<?php
$host = '127.0.0.1';
$port = '3307';
$db   = 'firmetna_new_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$hashedPassword = '$2y$10$/UQmSM98RGu7OaaU9kHCTmXdrsfUiC2Yvjj5BpW9'; // admin123

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     $email = 'admin@firmetna.com';
     
     // Check if exists
     $stmt = $pdo->prepare('SELECT id FROM user WHERE email = ?');
     $stmt->execute([$email]);
     if ($stmt->fetch()) {
         // Update existing
         $stmt = $pdo->prepare('UPDATE user SET role = "ROLE_ADMIN", password = ? WHERE email = ?');
         $stmt->execute([$hashedPassword, $email]);
         echo "User $email updated to ROLE_ADMIN and password reset to admin123.\n";
     } else {
         // Insert new
         $stmt = $pdo->prepare('INSERT INTO user (email, password, role, nom, prenom, statut, role_type, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
         $stmt->execute([
             $email, 
             $hashedPassword, 
             'ROLE_ADMIN', 
             'Admin', 
             'Firmetna', 
             'Actif', 
             'Administrateur', 
             date('Y-m-d H:i:s')
         ]);
         echo "Admin user created successfully!\n";
     }
} catch (\PDOException $e) {
     echo "Database error: " . $e->getMessage();
}
