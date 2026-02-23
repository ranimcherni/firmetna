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
     
     echo "--- SPECIFIC CHECK ---\n";
     $stmt = $pdo->prepare('SELECT email, role FROM user WHERE email = ?');
     $stmt->execute(['admin@firmetna.com']);
     $user = $stmt->fetch();
     if ($user) {
         echo "FOUND admin@firmetna.com | Role: " . $user['role'] . "\n";
     } else {
         echo "NOT FOUND: admin@firmetna.com\n";
     }
     
     echo "--- ANY ROLE_ADMIN ---\n";
     $stmt = $pdo->query("SELECT email, role FROM user WHERE role LIKE '%ADMIN%'");
     $admins = $stmt->fetchAll();
     if (count($admins) > 0) {
         foreach ($admins as $admin) {
             echo "Email: " . $admin['email'] . " | Role: " . $admin['role'] . "\n";
         }
     } else {
         echo "No users with 'ADMIN' in role found.\n";
     }
     
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
