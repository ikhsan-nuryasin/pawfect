<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$username = $_SESSION['user'];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pet = trim($_POST['nama_pet'] ?? '');
    $jenis_pet = trim($_POST['jenis_pet'] ?? '');
    $umur = trim($_POST['umur'] ?? '');
    $foto = trim($_POST['foto'] ?? 'https://via.placeholder.com/100');

    if ($nama_pet && $jenis_pet && $umur) {
        $stmt = $conn->prepare('INSERT INTO pets (username, nama_pet, jenis_pet, umur, foto) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $username, $nama_pet, $jenis_pet, $umur, $foto);

        if ($stmt->execute()) {
            $message = '🎉 Pet berhasil ditambahkan!';
            $messageType = 'success';
            header('refresh:2;url=profile.php');
        } else {
            $message = 'Error: ' . $conn->error;
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Semua field harus diisi!';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet - Pawfect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #d7e2db;
            max-width: 430px;
            margin: auto;
            min-height: 100vh;
            padding-bottom: 100px;
            color: #5f433a;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            color: #8b7355;
            font-size: 14px;
        }

        .message {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-card {
            background: #f1e8df;
            border: 1px solid #9d7c6f;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #5f433a;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #9d7c6f;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: #5f433a;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #7b5144;
            background: #fffaf5;
        }

        .pet-type-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .pet-type-btn {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            color: #5f433a;
        }

        .pet-type-btn:hover {
            border-color: #9d7c6f;
            background: #fffaf5;
        }

        .pet-type-btn.selected {
            border-color: #9a6f60;
            background: #9a6f60;
            color: white;
        }

        .age-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .image-preview {
            margin-top: 10px;
            text-align: center;
        }

        .image-preview img {
            max-width: 120px;
            border-radius: 8px;
            border: 1px solid #9d7c6f;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
        }

        .btn-save {
            background: #9a6f60;
            color: white;
        }

        .btn-save:hover {
            background: #8b5f50;
        }

        .btn-back {
            background: #ddd;
            color: #333;
        }

        .btn-back:hover {
            background: #ccc;
        }

        .navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 430px;
            width: 100%;
            margin: auto;
            background: #f7efe9;
            border-radius: 20px 20px 0 0;
            padding: 10px 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -4px 12px rgba(0,0,0,.08);
            border: 1px solid rgba(0,0,0,.05);
            z-index: 999;
        }

        .navbar a {
            text-decoration: none;
            color: #5f433a;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 4px 0;
        }

        .navbar a .icon {
            font-size: 22px;
            display: block;
        }

        .navbar a.active {
            color: #8b5b41;
        }

        .navbar a.active .icon {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐾 Tambah Pet</h1>
            <p>Tambahkan hewan peliharaan Anda</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Pet</label>
                    <input type="text" name="nama_pet" required placeholder="Contoh: Fluffy">
                </div>

                <div class="form-group">
                    <label>Jenis Pet</label>
                    <select name="jenis_pet" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Anjing">🐕 Anjing</option>
                        <option value="Kucing">🐈 Kucing</option>
                        <option value="Burung">🦜 Burung</option>
                        <option value="Kelinci">🐰 Kelinci</option>
                        <option value="Hamster">🐹 Hamster</option>
                        <option value="Lainnya">❓ Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Umur</label>
                    <div class="age-inputs">
                        <input type="number" name="umur_tahun" placeholder="Tahun" min="0">
                        <input type="number" name="umur_bulan" placeholder="Bulan" min="0" max="11">
                    </div>
                </div>

                <div class="form-group">
                    <label>URL Foto Pet (Opsional)</label>
                    <input type="url" name="foto" placeholder="https://example.com/foto.jpg">
                    <div class="image-preview">
                        <img id="preview" src="https://via.placeholder.com/100" alt="Preview">
                    </div>
                </div>

                <div class="buttons">
                    <button type="submit" class="btn btn-save">Tambah Pet</button>
                    <a href="profile.php" class="btn btn-back">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <div class="navbar">
        <a href="dashboard.php" class="nav-item"><span class="icon">🏠</span>Home</a>
        <a href="shop.php" class="nav-item"><span class="icon">🛒</span>Explore</a>
        <a href="basket.php" class="nav-item"><span class="icon">👜</span>Basket</a>
        <a href="favorite.php" class="nav-item"><span class="icon">❤️</span>Favorite</a>
        <a href="profile.php" class="nav-item active"><span class="icon">👤</span>Profile</a>
    </div>

    <script>
        const photoInput = document.querySelector('input[name="foto"]');
        const preview = document.getElementById('preview');

        photoInput.addEventListener('change', function() {
            if (this.value) {
                preview.src = this.value;
                preview.onerror = function() {
                    this.src = 'https://via.placeholder.com/100';
                };
            }
        });

        // Update preview on input blur
        photoInput.addEventListener('blur', function() {
            if (this.value) {
                preview.src = this.value;
                preview.onerror = function() {
                    this.src = 'https://via.placeholder.com/100';
                };
            }
        });
    </script>
</body>
</html>
