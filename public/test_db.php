<?php
$host = '127.0.0.1';
$port = '3307';
$user = 'root';
$pass = '';

echo "Connecting to MySQL on $host:$port...\n";
$start = microtime(true);
$mysqli = new mysqli($host, $user, $pass, '', $port);

if ($mysqli->connect_error) {
    echo "Connection failed: " . $mysqli->connect_error . "\n";
} else {
    echo "Connected successfully!\n";
    $mysqli->close();
}
$end = microtime(true);
echo "Time taken: " . ($end - $start) . " seconds\n";
?>
