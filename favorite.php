<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$products = require __DIR__ . '/products.php';
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';

// Build favorite items from DB if available, otherwise fallback to products.php
$favoriteItems = [];
$favIds = array_keys($_SESSION['favorites']);
if (!empty($favIds)) {
    require_once __DIR__ . '/db.php';

    $hasCategory = false;
    $categoryColumn = "NULL AS category";
    $categoryResult = $conn->query("SHOW COLUMNS FROM produk LIKE 'kategori'");
    if ($categoryResult && $categoryResult->num_rows > 0) {
        $hasCategory = true;
        $categoryColumn = 'kategori AS category';
    }

    $ids = array_map('intval', $favIds);
    $in = implode(',', $ids);
    if ($in !== '') {
        $sql = "SELECT id, nama_produk AS name, deskripsi AS description, harga AS price, gambar AS image_url, $categoryColumn FROM produk WHERE id IN ($in)";
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $favoriteItems[$row['id']] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'category' => $row['category'] ?? ($row['name'] ?? ''),
                    'image_url' => $row['image_url'] ?? '',
                ];
            }
        }
    }

    // For any favorite IDs not found in DB, take from fallback products.php
    foreach ($favIds as $id) {
        $id = intval($id);
        if (!isset($favoriteItems[$id]) && isset($products[$id])) {
            $favoriteItems[$id] = [
                'id' => $products[$id]['id'],
                'name' => $products[$id]['name'],
                'description' => $products[$id]['description'],
                'price' => $products[$id]['price'],
                'category' => $products[$id]['category'] ?? 'accessories',
                'image_url' => $products[$id]['image_url'] ?? '',
            ];
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['add_cart'])){

        $id = (int)$_POST['product_id'];

        if(!isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id] = 0;
        }

        $_SESSION['cart'][$id]++;
        $message = "Produk ditambahkan ke keranjang";
    }

    elseif(isset($_POST['remove_favorite'])){

        $id = (int)$_POST['product_id'];

        unset($_SESSION['favorites'][$id]);
        $message = "Produk favorit dihapus";
    }
}

$username = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite - Pawfect</title>
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
    padding-bottom:90px;
}

.header{
    text-align:center;
    padding:30px 15px 20px;
}

.header h1{
    color:#5f433a;
    font-size:36px;
    font-weight:700;
}

.products{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
    padding:12px;
}

.card{
    position:relative;
    background:#f8f8f8;
    border:1px solid #b28a7b;
    border-radius:16px;
    padding:10px;
}

.image{
    height:140px;
    display:flex;
    justify-content:center;
    align-items:center;
}

.image img{
    max-width:100%;
    max-height:130px;
    object-fit:contain;
}

.favorite-btn{
    position:absolute;
    top:8px;
    right:8px;
}

.favorite-btn button{
    border:none;
    background:none;
    font-size:28px;
    color:#6b3e35;
    cursor:pointer;
}

.title{
    font-size:11px;
    font-weight:600;
    min-height:32px;
    color:#333;
    margin-top:6px;
}

.price{
    margin-top:4px;
    font-size:14px;
    color:#5f433a;
}

.cart-btn{
    width:100%;
    margin-top:10px;
    border:none;
    border-radius:20px;
    background:#9a6f60;
    color:white;
    height:34px;
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

.message{
    margin:10px 12px;
    padding:12px;
    background:#d4edda;
    color:#155724;
    border-radius:12px;
    text-align:center;
}

.empty-state{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:20px;
    padding:60px 20px;
    text-align:center;
    grid-column:1/-1;
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
    <div class="container">

    <div class="header">
        <h1>My Favorites</h1>
    </div>

    <?php if ($message): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="products">

        <?php if(empty($favoriteItems)): ?>
            <div class="empty-state">
                <div class="empty-icon">❤️</div>
                <div class="empty-title">Tidak Ada Favorit</div>
                <div class="empty-text">Simpan produk favorit Anda di sini</div>
                <a href="shop.php" class="continue-shopping">Jelajahi Produk</a>
            </div>
        <?php else: ?>
        <?php foreach($favoriteItems as $id => $product): ?>

        <div class="card">

            <form method="POST" class="favorite-btn">
                <input type="hidden" name="product_id" value="<?= $id ?>">
                <button type="submit" name="remove_favorite">♥</button>
            </form>

            <div class="image">
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="title">
                <?= htmlspecialchars($product['name']) ?>
            </div>

            <div class="price">
                Rp <?= number_format($product['price'],0,',','.') ?>
            </div>

            <form method="POST">
                <input type="hidden" name="product_id" value="<?= $id ?>">
                <button
                    type="submit"
                    name="add_cart"
                    class="cart-btn">
                    Add to cart
                </button>
            </form>

        </div>

        <?php endforeach; ?>
        <?php endif; ?>

    </div>

</div>

<div class="navbar">
    <a href="dashboard.php" class="nav-item"><span class="icon">🏠</span>Home</a>
    <a href="shop.php" class="nav-item"><span class="icon">🛒</span>Explore</a>
    <a href="basket.php" class="nav-item"><span class="icon">👜</span>Basket</a>
    <a href="favorite.php" class="nav-item active"><span class="icon">❤️</span>Favorite</a>
    <a href="profile.php" class="nav-item"><span class="icon">👤</span>Profile</a>
</div>
</body>
</html>
