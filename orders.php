<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$username = $_SESSION['user'];

// Get user orders/transactions
$orders = [];
$orderQuery = $conn->prepare("
    SELECT * FROM orders 
    WHERE username = ? 
    ORDER BY tanggal_pesan DESC 
    LIMIT 10
");

if ($orderQuery) {
    $orderQuery->bind_param('s', $username);
    $orderQuery->execute();
    $result = $orderQuery->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $orderQuery->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Pawfect</title>
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

        .order-card {
            background: #f1e8df;
            border: 1px solid #9d7c6f;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .order-id {
            font-weight: 700;
            color: #5f433a;
        }

        .order-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .order-details {
            font-size: 13px;
            line-height: 1.8;
        }

        .order-details p {
            margin-bottom: 5px;
        }

        .order-amount {
            font-size: 16px;
            font-weight: 700;
            color: #7b5144;
            margin-top: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #f1e8df;
            border-radius: 15px;
            border: 1px solid #9d7c6f;
        }

        .empty-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        .empty-text {
            color: #8b7355;
        }

        .shop-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 25px;
            background: #9a6f60;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
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
            <h1>📦 Order History</h1>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p class="empty-text">Belum ada pesanan</p>
                <a href="shop.php" class="shop-btn">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-id">Order #<?php echo htmlspecialchars($order['id']); ?></div>
                        <div class="order-status status-<?php echo strtolower($order['status'] ?? 'pending'); ?>">
                            <?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?>
                        </div>
                    </div>
                    <div class="order-details">
                        <p><strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($order['tanggal_pesan'] ?? 'now')); ?></p>
                        <p><strong>Item:</strong> <?php echo htmlspecialchars($order['total_item'] ?? 0); ?> produk</p>
                        <p><strong>Alamat:</strong> <?php echo htmlspecialchars(substr($order['alamat_pengiriman'] ?? '-', 0, 40)); ?>...</p>
                        <div class="order-amount">
                            Total: Rp <?php echo number_format($order['total_harga'] ?? 0, 0, ',', '.'); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
