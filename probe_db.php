<?php
$ports = [3306, 3307];
$user = "root";
$pass = "";

foreach ($ports as $port) {
    echo "--- CHECKING PORT $port ---\n";
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=$port", $user, $pass);
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($databases as $db) {
            echo "Database: $db\n";
            if (str_contains($db, 'firmetna')) {
                try {
                    $pdoChild = new PDO("mysql:host=127.0.0.1;port=$port;dbname=$db", $user, $pass);
                    // Check if table publication exists
                    $tableExists = $pdoChild->query("SHOW TABLES LIKE 'publication'")->fetch();
                    if ($tableExists) {
                        $count = $pdoChild->query("SELECT COUNT(*) FROM publication")->fetchColumn();
                        echo "  [FOUND PUBLICATION TABLE] Rows: $count\n";
                        $latest = $pdoChild->query("SELECT id, titre FROM publication ORDER BY id DESC LIMIT 1")->fetch();
                        if ($latest) {
                            echo "  Latest ID: " . $latest['id'] . " - Title: " . $latest['titre'] . "\n";
                        }
                    }
                } catch (Exception $e) {}
            }
        }
    } catch (PDOException $e) {
        echo "Port $port: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
