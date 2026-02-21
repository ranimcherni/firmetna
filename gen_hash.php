<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);
file_put_contents('hash.txt', $hash);
echo "Hash generated and saved to hash.txt\n";
