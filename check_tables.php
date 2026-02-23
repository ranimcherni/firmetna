<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=firmetna_new_db', 'root', '');
    $stmt = $pdo->query('SHOW TABLES');
    while ($row = $stmt->fetch()) {
        echo $row[0] . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
