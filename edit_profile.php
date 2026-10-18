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

$stmt = $conn->prepare('SELECT nama, email FROM user WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nama && $email) {
        $stmt = $conn->prepare('UPDATE user SET nama = ?, email = ? WHERE username = ?');
        $stmt->bind_param('sss', $nama, $email, $username);

        if ($stmt->execute()) {
            $message = 'Profil berhasil diperbarui!';
            $messageType = 'success';
            $userData['nama'] = $nama;
            $userData['email'] = $email;
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
    <title>Edit Profile - Pawfect</title>
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
            padding: 20px;
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
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #9d7c6f;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: #5f433a;
        }

        .form-group input:focus {
            outline: none;
            border-color: #7b5144;
            background: #fffaf5;
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
            <h1>📝 Edit Profile</h1>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required value="<?php echo htmlspecialchars($userData['nama'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" disabled value="<?php echo htmlspecialchars($username); ?>">
                    <small style="color: #999; font-size: 12px;">Username tidak dapat diubah</small>
                </div>

                <div class="buttons">
                    <button type="submit" class="btn btn-save">Simpan Perubahan</button>
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
</body>
</html>
