<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$username = $_SESSION['user'];

// Get user points/rewards
$points = 0;
$stmt = $conn->prepare('SELECT poin FROM user WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $points = $row['poin'] ?? 0;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards - Pawfect</title>
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

        .points-card {
            background: linear-gradient(135deg, #9a6f60 0%, #7b5144 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            color: white;
            text-align: center;
        }

        .points-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .points-amount {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .points-description {
            font-size: 12px;
            opacity: 0.8;
        }

        .reward-item {
            background: #f1e8df;
            border: 1px solid #9d7c6f;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reward-info h3 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .reward-info p {
            font-size: 12px;
            color: #8b7355;
        }

        .reward-cost {
            background: #9a6f60;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            text-align: center;
        }

        .reward-cost.available {
            background: #28a745;
        }

        .reward-cost.unavailable {
            background: #ccc;
            color: #666;
        }

        .reward-section-title {
            font-size: 18px;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
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
            <h1>🎁 Rewards & Points</h1>
        </div>

        <div class="points-card">
            <div class="points-label">Poin Anda</div>
            <div class="points-amount"><?php echo number_format($points); ?></div>
            <div class="points-description">Kumpulkan poin dari setiap pembelian</div>
        </div>

        <div class="reward-section-title">Dapatkan Reward</div>

        <div class="reward-item">
            <div class="reward-info">
                <h3>💳 Diskon 5%</h3>
                <p>Untuk pembelian berikutnya</p>
            </div>
            <div class="reward-cost <?php echo $points >= 100 ? 'available' : 'unavailable'; ?>">
                100 poin
            </div>
        </div>

        <div class="reward-item">
            <div class="reward-info">
                <h3>📦 Gratis Ongkir</h3>
                <p>Pengiriman gratis untuk pesanan berikutnya</p>
            </div>
            <div class="reward-cost <?php echo $points >= 250 ? 'available' : 'unavailable'; ?>">
                250 poin
            </div>
        </div>

        <div class="reward-item">
            <div class="reward-info">
                <h3>🎟️ Voucher Rp 50.000</h3>
                <p>Gunakan untuk pembelian apapun</p>
            </div>
            <div class="reward-cost <?php echo $points >= 500 ? 'available' : 'unavailable'; ?>">
                500 poin
            </div>
        </div>

        <div class="reward-item">
            <div class="reward-info">
                <h3>💎 Member Premium</h3>
                <p>Akses eksklusif dan benefit premium</p>
            </div>
            <div class="reward-cost <?php echo $points >= 1000 ? 'available' : 'unavailable'; ?>">
                1000 poin
            </div>
        </div>

        <div class="reward-section-title">Cara Dapatkan Poin</div>

        <div class="reward-item">
            <div class="reward-info">
                <h3>🛍️ Pembelian Produk</h3>
                <p>1 poin per Rp 10.000 belanja</p>
            </div>
        </div>

        <div class="reward-item">
            <div class="reward-info">
                <h3>⭐ Rating Produk</h3>
                <p>10 poin per review produk</p>
            </div>
        </div>

        <div class="reward-item">
            <div class="reward-info">
                <h3>📱 Share & Referral</h3>
                <p>50 poin jika teman berhasil mendaftar</p>
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
