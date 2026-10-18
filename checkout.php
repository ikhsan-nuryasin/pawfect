<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$products = require __DIR__ . '/products.php';
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = array_intersect_key($products, $_SESSION['cart']);
$totalPrice = 0;
foreach ($cartItems as $id => $product) {
    $totalPrice += $product['price'] * ($_SESSION['cart'][$id] ?? 1);
}

$fullname = '';
$address = '';
$phone = '';
$payment = 'Transfer Bank';
$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $payment = trim($_POST['payment'] ?? 'Transfer Bank');

    if (empty($cartItems)) {
        $errors[] = 'Keranjang kosong. Tambahkan produk terlebih dahulu.';
    }
    if ($fullname === '') {
        $errors[] = 'Nama penerima wajib diisi.';
    } elseif (mb_strlen($fullname) > 100) {
        $errors[] = 'Nama penerima maksimal 100 karakter.';
    }
    if ($address === '') {
        $errors[] = 'Alamat wajib diisi.';
    } elseif (mb_strlen($address) > 500) {
        $errors[] = 'Alamat maksimal 500 karakter.';
    }
    if ($phone === '-') {
        // No phone collected from checkout form, skip strict validation.
    } elseif ($phone === '') {
        $errors[] = 'Nomor telepon wajib diisi.';
    } elseif (!preg_match('/^[0-9+\s-]{7,20}$/', $phone)) {
        $errors[] = 'Nomor telepon tidak valid.';
    }

    $allowedPayments = ['QRIS', 'Transfer Bank', 'COD', 'E-Wallet'];
    if (!in_array($payment, $allowedPayments, true)) {
        $payment = 'Transfer Bank';
    }

    if (empty($errors)) {
        if (!isset($_SESSION['orders'])) {
            $_SESSION['orders'] = [];
        }

        $orderId = 'ORD' . date('YmdHis') . rand(100, 999);
        $_SESSION['orders'][] = [
            'id' => $orderId,
            'user' => $_SESSION['user'],
            'fullname' => $fullname,
            'address' => $address,
            'phone' => $phone,
            'payment' => $payment,
            'items' => [],
            'total' => $totalPrice,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        foreach ($cartItems as $id => $product) {
            $_SESSION['orders'][count($_SESSION['orders']) - 1]['items'][] = [
                'id' => $id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $_SESSION['cart'][$id] ?? 1,
            ];
        }

        $_SESSION['cart'] = [];
        $cartItems = [];
        $totalPrice = 0;
        $message = 'Checkout berhasil. Pesananmu telah disimpan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Pawfect</title>
    <style>
        *{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#d7e2db;
    max-width:430px;
    margin:auto;
    min-height:100vh;
    color:#5f433a;
}

.container{
    padding:20px 14px 120px;
}

.header{
    text-align:center;
    margin:25px 0;
}

.header h1{
    font-size:38px;
    font-weight:700;
}

.order-card{
    background:#f1e8df;
    border:1px solid #9c786c;
    border-radius:18px;
    padding:10px;
    margin-bottom:10px;
    display:flex;
    gap:10px;
    align-items:flex-start;
}

.product-image{
    width:70px;
    height:70px;
    border:1px solid #9c786c;
    border-radius:12px;
    overflow:hidden;
    background:white;
    flex-shrink:0;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:contain;
}

.product-info{
    flex:1;
}

.product-name{
    font-size:13px;
    font-weight:600;
    margin-bottom:4px;
}

.product-price{
    font-size:14px;
}

.qty-box{
    margin-top:8px;
    width:90px;
    border:1px solid #9c786c;
    border-radius:20px;
    text-align:center;
    padding:3px;
    font-size:14px;
    font-weight:600;
    background:white;
}

.summary{
    background:#f1e8df;
    border:1px solid #9c786c;
    border-radius:18px;
    padding:12px;
    margin-top:10px;
}

.summary strong{
    font-size:18px;
}

.summary textarea{
    width:100%;
    margin-top:8px;
    border:1px solid #9c786c;
    border-radius:12px;
    padding:10px;
    resize:none;
    height:90px;
}

.payment-title{
    text-align:center;
    margin:20px 0;
    font-size:20px;
    font-weight:700;
}

.payment-grid{
    display:flex;
    justify-content:space-around;
    gap:10px;
}

.payment-option{
    text-align:center;
}

.payment-option input{
    display:none;
}

.payment-option label{
    cursor:pointer;
    display:block;
}

.payment-option img{
    width:60px;
    height:60px;
    object-fit:contain;
    border-radius:12px;
    border:2px solid transparent;
}

.payment-option input:checked + label img{
    border-color:#8b6458;
}

.payment-option span{
    display:block;
    margin-top:5px;
    font-weight:600;
}

.message{
    margin:16px 0;
    padding:14px;
    background:#d4edda;
    color:#155724;
    border-radius:14px;
    text-align:center;
}

.errors{
    margin:16px 0;
    padding:14px;
    background:#f8d7da;
    color:#721c24;
    border-radius:14px;
}

.errors div{
    margin-bottom:8px;
}

.submit-btn{
    width:100%;
    height:50px;
    margin-top:25px;
    border:none;
    border-radius:30px;
    background:#9a6f60;
    color:white;
    font-size:16px;
    font-weight:600;
}
</style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>My Orders</h1>
    </div>

    <?php if ($message): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div class="message">
            Keranjang kosong. Tambahkan produk terlebih dahulu.
        </div>
    <?php endif; ?>

    <?php foreach($cartItems as $id => $product): ?>

    <div class="order-card">

        <div class="product-image">
            <img src="<?= htmlspecialchars($product['image_url']) ?>">
        </div>

        <div class="product-info">

            <div class="product-name">
                <?= htmlspecialchars($product['name']) ?>
            </div>

            <div class="product-price">
                Rp <?= number_format($product['price'],0,',','.') ?>
            </div>

            <div class="qty-box">
                Qty : <?= $_SESSION['cart'][$id] ?>
            </div>

        </div>

    </div>

    <?php endforeach; ?>

    <form method="POST">

        <div class="summary">

            <div>
                <strong>Total Price :</strong>
                Rp <?= number_format($totalPrice,0,',','.') ?>
            </div>

            <div style="margin-top:8px;font-weight:600;">
                Address :
            </div>

            <textarea
                name="address"
                placeholder="Put your address here"
                required><?= htmlspecialchars($address) ?></textarea>

            <input type="hidden"
                   name="fullname"
                   value="<?= htmlspecialchars($_SESSION['user']) ?>">

            <input type="hidden"
                   name="phone"
                   value="-">

        </div>

        <div class="payment-title">
            payment via
        </div>

        <div class="payment-grid">

            <div class="payment-option">
                <input type="radio"
                       name="payment"
                       value="QRIS"
                       id="qris"
                       checked>

                <label for="qris">
                    <img src="images/qris.png">
                    <span>Qris</span>
                </label>
            </div>

            <div class="payment-option">
                <input type="radio"
                       name="payment"
                       value="COD"
                       id="cod">

                <label for="cod">
                    <img src="images/cod.png">
                    <span>COD</span>
                </label>
            </div>

            <div class="payment-option">
                <input type="radio"
                       name="payment"
                       value="Transfer Bank"
                       id="bank">

                <label for="bank">
                    <img src="images/bank.png">
                    <span>MBanking</span>
                </label>
            </div>

        </div>

        <button class="submit-btn" type="submit">
            Place Order
        </button>

    </form>

</div>
</div>
</body>
</html>
