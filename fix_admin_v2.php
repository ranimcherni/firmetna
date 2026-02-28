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

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     $email = 'admin@firmetna.com';
     $password = 'admin123';
     $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
     
     echo "Updating $email with hash: $hashedPassword\n";
     
     $stmt = $pdo->prepare('UPDATE user SET password = ?, role = "ROLE_ADMIN" WHERE email = ?');
     $stmt->execute([$hashedPassword, $email]);
     
     if ($stmt->rowCount() > 0) {
         echo "Success: Admin account updated.\n";
     } else {
         echo "Notice: Email not found or already up to date. Checking if user exists...\n";
         $stmt = $pdo->prepare('SELECT id FROM user WHERE email = ?');
         $stmt->execute([$email]);
         if (!$stmt->fetch()) {
             echo "Error: User $email DOES NOT EXIST. Use the previous insert script first.\n";
         } else {
             echo "User exists, maybe the password was already correct.\n";
         }
     }
} catch (\PDOException $e) {
     echo "Database error: " . $e->getMessage();
}
