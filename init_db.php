<?php
require 'db.php';

// Create tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS user (
        username VARCHAR(50) PRIMARY KEY,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        nama VARCHAR(100),
        poin INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS produk (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_produk VARCHAR(100) NOT NULL,
        deskripsi TEXT,
        harga DECIMAL(10, 2),
        kategori VARCHAR(50),
        gambar VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(50) DEFAULT 'pending',
        total_item INT DEFAULT 0,
        alamat_pengiriman TEXT,
        total_harga DECIMAL(10, 2),
        FOREIGN KEY (username) REFERENCES user(username)
    )",

    "CREATE TABLE IF NOT EXISTS artikel (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(200) NOT NULL,
        konten TEXT,
        gambar VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS pets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        nama_pet VARCHAR(100),
        jenis_pet VARCHAR(50),
        umur VARCHAR(50),
        foto VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (username) REFERENCES user(username)
    )",

    "CREATE TABLE IF NOT EXISTS favorit (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        produk_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (username) REFERENCES user(username),
        FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
    )"
];

$success_count = 0;
$error_count = 0;

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        $success_count++;
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
        $error_count++;
    }
}

$conn->close();

echo "Database initialized successfully!<br>";
echo "Tables created: " . $success_count . "<br>";
if ($error_count > 0) {
    echo "Errors: " . $error_count . "<br>";
}
?>
