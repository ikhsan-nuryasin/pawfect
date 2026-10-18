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

$message = '';

$cartItems = array_intersect_key($products, $_SESSION['cart']);
$totalPrice = 0;
foreach ($cartItems as $id => $product) {
    $totalPrice += $product['price'] * ($_SESSION['cart'][$id] ?? 1);
}
$username = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int)($_POST['product_id'] ?? 0);

    if(isset($_POST['increase'])){

        if(isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id]++;
        }

    }elseif(isset($_POST['decrease'])){

        if(isset($_SESSION['cart'][$id])){

            $_SESSION['cart'][$id]--;

            if($_SESSION['cart'][$id] <= 0){
                unset($_SESSION['cart'][$id]);
            }
        }
    }

    header("Location: basket.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basket - Pawfect</title>
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
    padding-bottom:140px;
}

.header{
    text-align:center;
    padding:30px 15px;
}

.header h1{
    color:#5f433a;
    font-size:34px;
    font-weight:700;
}

.cart-list{
    padding:0 12px;
}

.cart-card{
    background:#f3ece5;
    border:1px solid #a97d6d;
    border-radius:18px;
    padding:12px;
    display:flex;
    gap:10px;
    margin-bottom:12px;
}

.cart-image{
    width:80px;
    height:80px;
    border:1px solid #a97d6d;
    border-radius:12px;
    overflow:hidden;
    background:white;
}

.cart-image img{
    width:100%;
    height:100%;
    object-fit:contain;
}

.cart-info{
    flex:1;
}

.cart-name{
    font-size:14px;
    font-weight:600;
    color:#3d3026;
}

.cart-price{
    margin-top:4px;
    color:#3d3026;
    font-size:15px;
}

.qty-form{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:10px;
    margin-top:10px;
}

.qty-btn{
    width:28px;
    height:28px;
    border-radius:50%;
    border:2px solid #6b4a3f;
    background:white;
    color:#6b4a3f;
    font-weight:bold;
    cursor:pointer;
}

.qty-number{
    font-size:22px;
    color:#6b4a3f;
    font-weight:700;
    min-width:25px;
    text-align:center;
}

.checkout{
    position:fixed;
    bottom:75px;
    left:50%;
    transform:translateX(-50%);
    width:85%;
    max-width:360px;
}

.checkout a{
    display:block;
    text-align:center;
    background:#9a6f60;
    color:white;
    text-decoration:none;
    padding:16px;
    border-radius:30px;
    font-weight:600;
}

.navbar{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    max-width:430px;
    width:100%;
    margin:auto;
    background:#f7efe9;
    border-radius:20px 20px 0 0;
    padding:10px 0;
    display:flex;
    justify-content:space-around;
    align-items:center;
    box-shadow:0 -4px 12px rgba(0,0,0,.08);
    border:1px solid rgba(0,0,0,.05);
    z-index:999;
}

.navbar a{
    text-decoration:none;
    color:#5f433a;
    font-size:11px;
    font-weight:600;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:4px;
    padding:4px 0;
}

.navbar a .icon{
    font-size:22px;
    display:block;
}

.navbar a.active{
    color:#8b5b41;
}

.navbar a.active .icon{
    transform:scale(1.1);
}

.empty-state{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:20px;
    padding:60px 20px;
    text-align:center;
}

.empty-icon{
    font-size:80px;
    animation:float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.empty-title{
    font-size:20px;
    font-weight:700;
    color:#5f433a;
}

.empty-text{
    font-size:14px;
    color:#8b7355;
}

.continue-shopping{
    display:inline-block;
    margin-top:10px;
    padding:12px 30px;
    background:#9a6f60;
    color:white;
    text-decoration:none;
    border-radius:20px;
    font-weight:600;
}
</style>
</head>
<body>
<div class="header">
    <h1>My Basket</h1>
</div>

<div class="cart-list">

<?php if(empty($cartItems)): ?>
    <div class="empty-state">
        <div class="empty-icon">👜</div>
        <div class="empty-title">Keranjang Kosong</div>
        <div class="empty-text">Belum ada produk di keranjang Anda</div>
        <a href="shop.php" class="continue-shopping">Lanjutkan Belanja</a>
    </div>
<?php else: ?>
<?php foreach($cartItems as $id => $product): ?>

<div class="cart-card">

    <div class="cart-image">
        <img src="<?= htmlspecialchars($product['image_url']) ?>">
    </div>

    <div class="cart-info">

        <div class="cart-name">
            <?= htmlspecialchars($product['name']) ?>
        </div>

        <div class="cart-price">
            Rp <?= number_format($product['price'],0,',','.') ?>
        </div>

        <form method="POST" class="qty-form">

            <input type="hidden"
                   name="product_id"
                   value="<?= $id ?>">

            <button class="qty-btn"
                    name="decrease">
                −
            </button>

            <div class="qty-number">
                <?= $_SESSION['cart'][$id] ?>
            </div>

            <button class="qty-btn"
                    name="increase">
                +
            </button>

        </form>

    </div>

</div>

<?php endforeach; ?>
<?php endif; ?>

</div>

<?php if(!empty($cartItems)): ?>
<div class="checkout">
    <a href="checkout.php">
        Proceed to checkout
    </a>
</div>
<?php endif; ?>

<div class="navbar">
    <a href="dashboard.php" class="nav-item"><span class="icon">🏠</span>Home</a>
    <a href="shop.php" class="nav-item"><span class="icon">🛒</span>Explore</a>
    <a href="basket.php" class="nav-item active"><span class="icon">👜</span>Basket</a>
    <a href="favorite.php" class="nav-item"><span class="icon">❤️</span>Favorite</a>
    <a href="profile.php" class="nav-item"><span class="icon">👤</span>Profile</a>
</div></body>
</html>
