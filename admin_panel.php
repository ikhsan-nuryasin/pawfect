<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pawfect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5e6d3 0%, #e8dcc9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #5f433a;
            font-size: 32px;
            margin-bottom: 5px;
        }

        .header p {
            color: #8b7355;
            font-size: 16px;
        }

        .welcome {
            background: #f5ece4;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #9a6f60;
        }

        .welcome-text {
            color: #5f433a;
            font-weight: 600;
            font-size: 16px;
        }

        .menu {
            display: grid;
            gap: 15px;
            margin-bottom: 30px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f9f7f4;
            border: 2px solid #ddd;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .menu-item:hover {
            background: #f5ece4;
            border-color: #9a6f60;
            transform: translateX(5px);
        }

        .menu-item .icon {
            font-size: 28px;
        }

        .menu-item .text h3 {
            color: #5f433a;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .menu-item .text p {
            color: #8b7355;
            font-size: 13px;
        }

        .logout {
            display: block;
            text-align: center;
            padding: 12px 30px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .logout:hover {
            background: #c82333;
        }

        .menu-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐾 Admin Panel</h1>
            <p>Pawfect - Pet Care Management</p>
        </div>

        <div class="welcome">
            <div class="welcome-text">
                Selamat datang, <?php echo htmlspecialchars($_SESSION['admin']); ?>!
            </div>
        </div>

        <div class="menu">
            <a href="admin_crud.php?section=products" class="menu-link">
                <div class="menu-item">
                    <div class="icon">📦</div>
                    <div class="text">
                        <h3>Kelola Produk</h3>
                        <p>Tambah, edit, dan hapus produk</p>
                    </div>
                </div>
            </a>

            <a href="admin_crud.php?section=users" class="menu-link">
                <div class="menu-item">
                    <div class="icon">👥</div>
                    <div class="text">
                        <h3>Kelola User</h3>
                        <p>Tambah, edit, dan hapus user</p>
                    </div>
                </div>
            </a>

            <a href="admin_crud.php?section=articles" class="menu-link">
                <div class="menu-item">
                    <div class="icon">📰</div>
                    <div class="text">
                        <h3>Kelola Artikel</h3>
                        <p>Tambah, edit, dan hapus artikel</p>
                    </div>
                </div>
            </a>
        </div>

        <a href="admin_logout.php" class="logout">Logout</a>
    </div>
</body>
</html>