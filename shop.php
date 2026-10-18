<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = intval($_POST['product_id'] ?? 0);

    $productQuery = $conn->prepare('SELECT id, nama_produk FROM produk WHERE id = ?');
    $productQuery->bind_param('i', $productId);
    $productQuery->execute();
    $result = $productQuery->get_result();
    $product = $result->fetch_assoc();
    $productQuery->close();
    // if product not in DB, check fallback products.php so actions still work
    if (!$product) {
        $fallbackProducts = require __DIR__ . '/products.php';
        if (isset($fallbackProducts[$productId])) {
            $product = [
                'id' => $fallbackProducts[$productId]['id'],
                'nama_produk' => $fallbackProducts[$productId]['name'],
            ];
        }
    }

    if (!$product) {
        $message = 'Produk tidak ditemukan.';
    } else {
        if ($action === 'add_cart') {
            if (!isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = 0;
            }
            $_SESSION['cart'][$productId] += 1;
            $message = 'Produk ditambahkan ke keranjang.';
        }
        if ($action === 'toggle_favorite') {

    if (isset($_SESSION['favorites'][$productId])) {

        unset($_SESSION['favorites'][$productId]);

        $message = 'Produk dihapus dari favorit.';

    } else {

        $_SESSION['favorites'][$productId] = true;

        $message = 'Produk ditambahkan ke favorit.';
    }
}
        }
    }


$categoryFilter = $_GET['category'] ?? 'all';
$searchTerm = trim($_GET['search'] ?? '');

$hasCategory = false;
$categoryColumn = 'NULL AS category';
$categoryResult = $conn->query("SHOW COLUMNS FROM produk LIKE 'kategori'");
if ($categoryResult && $categoryResult->num_rows > 0) {
    $hasCategory = true;
    $categoryColumn = 'kategori AS category';
}

$sql = "SELECT id, nama_produk AS name, deskripsi AS description, harga AS price, gambar AS image_url, $categoryColumn FROM produk";
$where = [];
$params = [];
$types = '';

if ($hasCategory && $categoryFilter !== 'all') {
    $where[] = 'kategori = ?';
    $params[] = $categoryFilter;
    $types .= 's';
}
if ($searchTerm !== '') {
    $where[] = '(nama_produk LIKE ? OR deskripsi LIKE ?)';
    $searchLike = '%' . $searchTerm . '%';
    $params[] = $searchLike;
    $params[] = $searchLike;
    $types .= 'ss';
}

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY name';

$products = [];
$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
} else {
    $message = 'Gagal memuat produk dari database.';
}

function guessCategory(array $item): string {
    $text = strtolower(($item['name'] ?? '') . ' ' . ($item['description'] ?? ''));
    if (preg_match('/\b(food|treat|dry|wet|cat treat|dog food|vaccine)\b/', $text)) {
        return 'food';
    }
    if (preg_match('/\b(shampoo|brush|dental|groom|care|vitamin|health)\b/', $text)) {
        return 'petcare';
    }
    if (preg_match('/\b(toy|bowl|feeder|dispenser|collar|accessories|chew|play)\b/', $text)) {
        return 'accessories';
    }
    if (preg_match('/\b(grooming)\b/', $text)) {
        return 'grooming';
    }
    return 'accessories';
}

$categories = ['all' => 'All'];
if ($hasCategory) {
    $categoryStmt = $conn->prepare('SELECT DISTINCT kategori FROM produk ORDER BY kategori');
    if ($categoryStmt) {
        $categoryStmt->execute();
        $categoryResult = $categoryStmt->get_result();
        while ($row = $categoryResult->fetch_assoc()) {
            $key = $row['kategori'];
            $categories[$key] = ucwords(str_replace(['_', '-'], ' ', $key));
        }
        $categoryStmt->close();
    }
} else {
    $categories += [
        'accessories' => 'Accessories',
        'food' => 'Food',
        'petcare' => 'Pet Care',
        'toys' => 'Toys',
        'grooming' => 'Grooming',
        'health' => 'Health',
    ];
}

if (!$hasCategory) {
    foreach ($products as &$product) {
        $product['category'] = guessCategory($product);
    }
    unset($product);

    if ($categoryFilter !== 'all') {
        $products = array_values(array_filter($products, function ($product) use ($categoryFilter) {
            return ($product['category'] ?? '') === $categoryFilter;
        }));
    }
}

if (empty($products)) {
    $fallbackProducts = require __DIR__ . '/products.php';
    foreach ($fallbackProducts as $product) {
        if (!isset($categories[$product['category']])) {
            $categories[$product['category']] = ucwords(str_replace(['_', '-'], ' ', $product['category']));
        }
        if ($categoryFilter !== 'all' && $product['category'] !== $categoryFilter) {
            continue;
        }
        if ($searchTerm !== '' && stripos($product['name'], $searchTerm) === false && stripos($product['description'], $searchTerm) === false) {
            continue;
        }
        $products[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'category' => $product['category'],
            'price' => $product['price'],
            'description' => $product['description'],
            'image_url' => $product['image_url'] ?? '',
        ];
    }
}

$cartCount = array_sum($_SESSION['cart']);
$favoritesCount = count($_SESSION['favorites']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop - Pawfect</title>
  <style>
      :root{:
    --brown:#6b4a3f;
    --brown-light:#9b7467;
    --bg:#dfe8e0;
    --card:#f8f8f8;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#dfe8e0;
    min-height:100vh;
}

.container{
    max-width:430px;
    margin:auto;
    padding-bottom:90px;
}

/* HEADER */

.header{
    padding:20px 12px 10px;
    background:#dfe8e0;
    border:none;
    box-shadow:none;
}

.header h1{
    text-align:center;
    color:var(--brown);
    font-size:24px;
    font-weight:700;
    margin-bottom:15px;
}

.header p{
    display:none;
}

/* SEARCH */

.search-panel{
    position:relative;
    margin-bottom:15px;
}

.search-panel input{
    width:100%;
    height:42px;
    border:1px solid var(--brown-light);
    border-radius:10px;
    padding:0 40px;
    background:#fff;
}

.search-panel button{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    background:none;
    border:none;
    color:var(--brown);
}

/* CATEGORY */
.categories{
    display:flex;
    align-items:center;
    gap:12px;
    overflow-x:auto;
    overflow-y:hidden;
    padding:12px 4px;
    margin-bottom:10px;
    scrollbar-width:none;
    -webkit-overflow-scrolling:touch;
}

.categories::-webkit-scrollbar{
    display:none;
}

.category-link{
    flex-shrink:0;
    text-decoration:none;
    color:#6b4a3f;
    font-size:15px;
    font-weight:600;
    padding:8px 0;
    transition:all .25s ease;
    white-space:nowrap;
}

.category-link:hover{
    color:#8d6257;
}

.category-link.active{
    background:#6b4a3f;
    color:white;
    padding:8px 18px;
    border-radius:20px;
    box-shadow:0 3px 8px rgba(107,74,63,.2);
}
/* HIDE STATS */

.stats{
    display:none;
}

/* PRODUCT GRID */

.products{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:10px;
    padding:10px;
}

.card{
    background:#f9f9f9;
    border-radius:15px;
    border:1px solid #b68e81;
    overflow:hidden;
    box-shadow:none;
    position:relative;
}

.image{
    height:140px;
    background:#f6f6f6;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:10px;
}

.image img{
    width:100%;
    height:100%;
    object-fit:contain;
}

.fav-button{
    position:absolute;
    top:8px;
    right:8px;
    width:35px;
    height:35px;
    background:none;
    border:none;
    box-shadow:none;
}

.fav-button button{
    font-size:26px;
    color:var(--brown);
}

.card-body{
    padding:10px;
}

.category-badge{
    display:none;
}

.card h3{
    font-size:11px;
    line-height:1.3;
    color:#333;
    height:34px;
    overflow:hidden;
}

.card p{
    display:none;
}

.price{
    font-size:13px;
    color:#555;
    margin-top:4px;
}

.actions{
    margin-top:8px;
}

.add-cart{
    width:100%;
    height:34px;
    border:none;
    border-radius:20px;
    background:var(--brown-light);
    color:white;
    font-weight:600;
}

/* NAVBAR */

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
    color:var(--brown);
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
  </style>
</head>
<body>
<div class="container">
 <div class="header">
    <h1>Shop</h1>

    <form class="search-panel" method="GET">
        <input type="search"
               name="search"
               placeholder="Search"
               value="<?= htmlspecialchars($searchTerm) ?>">

        <button type="submit">✕</button>

        <input type="hidden"
               name="category"
               value="<?= htmlspecialchars($categoryFilter) ?>">
    </form>

    <div class="categories">
        <?php foreach ($categories as $key => $label): ?>
            <a class="category-link <?= $categoryFilter === $key ? 'active' : '' ?>"
               href="?category=<?= urlencode($key) ?>&search=<?= urlencode($searchTerm) ?>">
               <?= htmlspecialchars($label) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="products">
        <?php if (empty($products)): ?>
            <div class="empty-state">Tidak ada produk yang cocok. Coba kategori atau kata kunci lain.</div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <div class="image">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#8d7b6b;font-size:13px;padding:14px;">No image</div>
                        <?php endif; ?>
                       <form method="POST" class="fav-button">

    <input type="hidden"
           name="product_id"
           value="<?= $product['id'] ?>">

    <button
        type="submit"
        name="action"
        value="toggle_favorite"
        style="all:unset;
               cursor:pointer;
               width:100%;
               height:100%;
               display:flex;
               align-items:center;
               justify-content:center;">

        <?= isset($_SESSION['favorites'][$product['id']])
            ? '❤️'
            : '🤍' ?>

    </button>

</form>
                    </div>
                    <div class="card-body">
                        <span class="category-badge"><?= htmlspecialchars(ucwords(str_replace(['_', '-'], ' ', $product['category']))) ?></span>
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <div class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                        <form method="POST" class="actions">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button class="add-cart" type="submit" name="action" value="add_cart">Add to cart</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="navbar">
    <a href="dashboard.php" class="nav-item"><span class="icon">🏠</span>Home</a>
    <a href="shop.php" class="nav-item active"><span class="icon">🛒</span>Explore</a>
    <a href="basket.php" class="nav-item"><span class="icon">👜</span>Basket</a>
    <a href="favorite.php" class="nav-item"><span class="icon">❤️</span>Favorite</a>
    <a href="profile.php" class="nav-item"><span class="icon">👤</span>Profile</a>
</div>
</body>
</html>