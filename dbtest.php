<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "<h1>DB Connection Test</h1>";

echo "db.php exists: " . (file_exists('db.php') ? 'yes' : 'no') . "<br>";

echo "is_readable db.php: " . (is_readable('db.php') ? 'yes' : 'no') . "<br>";

require 'db.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    echo "Error: koneksi database tidak dibuat.<br>";
    exit;
}

echo "Koneksi berhasil!<br>";
echo "Host: " . htmlspecialchars($host) . "<br>";
echo "Database: " . htmlspecialchars($db) . "<br>";
echo "Client info: " . mysqli_get_client_info() . "<br>";
echo "Server info: " . $conn->server_info . "<br>";
echo "Charset: " . $conn->character_set_name() . "<br>";

$result = $conn->query('SELECT 1');
if ($result) {
    echo "Simple query OK.<br>";
    $result->close();
} else {
    echo "Query error: " . htmlspecialchars($conn->error) . "<br>";
}
?>