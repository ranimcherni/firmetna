<?php
$user = "root";
$pass = "";
foreach ([3306, 3307] as $port) {
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=$port", $user, $pass);
        foreach ($pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN) as $db) {
            if (str_contains($db, 'firmetna')) {
                $pdoChild = new PDO("mysql:host=127.0.0.1;port=$port;dbname=$db", $user, $pass);
                if ($pdoChild->query("SHOW TABLES LIKE 'offre'")->fetch()) {
                    $count = $pdoChild->query("SELECT COUNT(*) FROM offre")->fetchColumn();
                    echo "Port $port, DB $db, Table 'offre' exists. Rows: $count\n";
                    foreach ($pdoChild->query("SELECT * FROM offre ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        print_r($row);
                    }
                }
                if ($pdoChild->query("SHOW TABLES LIKE 'demande'")->fetch()) {
                    $count = $pdoChild->query("SELECT COUNT(*) FROM demande")->fetchColumn();
                    echo "Port $port, DB $db, Table 'demande' exists. Rows: $count\n";
                }
            }
        }
    } catch (Exception $e) {}
}
