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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'change_password') {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$oldPassword || !$newPassword || !$confirmPassword) {
            $message = 'Semua field harus diisi!';
            $messageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'Password baru tidak cocok!';
            $messageType = 'error';
        } else {
            $stmt = $conn->prepare('SELECT password FROM user WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $userData = $result->fetch_assoc();
            $stmt->close();

            if (password_verify($oldPassword, $userData['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE user SET password = ? WHERE username = ?');
                $stmt->bind_param('ss', $hashedPassword, $username);

                if ($stmt->execute()) {
                    $message = 'Password berhasil diubah!';
                    $messageType = 'success';
                } else {
                    $message = 'Error: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
            } else {
                $message = 'Password lama tidak sesuai!';
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security - Pawfect</title>
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
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
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

        .section-card {
            background: #f1e8df;
            border: 1px solid #9d7c6f;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #9d7c6f;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 14px;
            color: #5f433a;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #9d7c6f;
            border-radius: 6px;
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
            margin-top: 15px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
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

        .btn-reset {
            background: #ddd;
            color: #333;
        }

        .btn-reset:hover {
            background: #ccc;
        }

        .notification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #ddd;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-label {
            font-size: 14px;
            color: #5f433a;
        }

        .toggle-switch {
            width: 50px;
            height: 28px;
            background: #ccc;
            border-radius: 14px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch.on {
            background: #9a6f60;
        }

        .toggle-switch .slider {
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
            transition: left 0.3s;
        }

        .toggle-switch.on .slider {
            left: 24px;
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
            <h1>🔔 Security Settings</h1>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="section-card">
            <div class="section-title">🔐 Ubah Password</div>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label>Password Lama</label>
                    <input type="password" name="old_password" required>
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <div class="buttons">
                    <button type="submit" class="btn btn-save">Ubah Password</button>
                    <button type="reset" class="btn btn-reset">Batal</button>
                </div>
            </form>
        </div>

        <div class="section-card">
            <div class="section-title">🔔 Notifikasi</div>
            <div class="notification-item">
                <span class="notification-label">Email Notifikasi</span>
                <div class="toggle-switch on">
                    <div class="slider"></div>
                </div>
            </div>
            <div class="notification-item">
                <span class="notification-label">Push Notifikasi</span>
                <div class="toggle-switch on">
                    <div class="slider"></div>
                </div>
            </div>
            <div class="notification-item">
                <span class="notification-label">Promo & Penawaran</span>
                <div class="toggle-switch on">
                    <div class="slider"></div>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-title">🛡️ Session Aktif</div>
            <div class="notification-item">
                <div>
                    <div style="font-weight: 600;">Device Ini</div>
                    <div style="font-size: 12px; color: #8b7355;">Logged in now</div>
                </div>
                <div style="color: #28a745; font-weight: 600;">✓ Aktif</div>
            </div>
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
