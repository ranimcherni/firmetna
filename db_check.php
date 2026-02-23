<?php
require 'vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$dbUrl = $_ENV['DATABASE_URL'];
// Split DATABASE_URL: mysql://root:@127.0.0.1:3307/firmetna_new_db?...
preg_match('/mysql:\/\/([^:]*):([^@]*)@([^:]*):(\d+)\/(.*)/', $dbUrl, $matches);
$user = $matches[1];
$pass = $matches[2];
$host = $matches[3];
$port = $matches[4];
$db = explode('?', $matches[5])[0];

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$result = $conn->query("SHOW COLUMNS FROM demande");
while($row = $result->fetch_assoc()) {
    print_r($row);
}
$conn->close();
