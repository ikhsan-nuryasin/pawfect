<?php
session_start();

// cek login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pawfect Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
   background-image:url("assets/latar.png");
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
    min-height:100vh;
    overflow-x:hidden;
    padding-bottom:90px;
    position:relative;
}

/* wave top */
.wave-top{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    z-index:0;
}

/* container */
.container{
    position:relative;
    z-index:2;
}

/* header */
.header{
    text-align:center;
    padding-top:20px;
}

.logo{
    width:230px;
    margin-top:20px;
}

.welcome{
    text-align:left;
    padding:15px;
    font-size:20px;
    font-weight:500;
}

/* menu */
.menu{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:8px;
    padding:0 10px;
}

.item{
    background:#f5ece4;
    border:1px solid #8d6257;
    border-radius:15px;
    text-align:center;
    padding:10px;
    text-decoration:none;
    color:#5b4038;
}

.item img{
    width:45px;
    height:45px;
    object-fit:contain;
}

.item p{
    margin-top:5px;
    font-size:12px;
}

/* notif */
.notif{
    margin:20px 10px;
    background:#f5ece4;
    border:1px solid #8d6257;
    border-radius:15px;
    padding:15px;
    display:flex;
    align-items:center;
    gap:10px;
}
.notif .icon{
    font-size:30px;
}

.notif p{
    font-size:13px;
    font-weight:600;
    color:#5a3e36;
}

/* article */
.articles{
    background:#f5ece4;
    margin:20px 10px;
    border-radius:15px;
    border:1px solid #8d6257;
    padding:15px;
}

.articles h2{
    text-align:center;
    margin-bottom:15px;
}

.article{
    display:flex;
    gap:10px;
    margin-bottom:15px;
}

.article img{
    width:90px;
    height:90px;
    border-radius:5px;
    object-fit:cover;
}

.article-text{
    font-size:12px;
}

.article-text h4{
    font-size:12px;
    margin-bottom:5px;
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
    color:#6b4a3f;
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

/* mobile */
@media(max-width:480px){

    .menu{
        grid-template-columns:repeat(4,1fr);
    }

    .item img{
        width:38px;
    }

    .item p{
        font-size:10px;
    }

    .article img{
        width:90px;
        height:90px;
    }

    .article-text p{
        font-size:10px;
    }
}

</style>
</head>

<body>

<div class="container">


    <!-- header -->
  <div class="header">

    <img src="assets/logo1-removebg-preview.png" class="logo">

    <div class="welcome">
        Hi <?= htmlspecialchars($username) ?>, welcome!
    </div>

</div>
    <!-- menu -->
    <div class="menu">

        <a href="doctor.php" class="item">
            <img src="assets/doctor.png">
            <p>Doctor</p>
        </a>

        <a href="shop.php" class="item">
            <img src="assets/shop.png">
            <p>Shop</p>
        </a>

        <a href="petboarding.php" class="item">
            <img src="assets/pet.png">
            <p>Pet Boarding</p>
        </a>

        <a href="pesan.php" class="item">
            <img src="assets/pesan.png">
            <p>Chat</p>
        </a>

    </div>

    <!-- notif -->
    <div class="notif">

        <div class="icon">
            🔔
        </div>

        <p>
            Get 5% Discount for petcare each time you bring your pet's dump!!!
        </p>

    </div>

   <!-- articles -->
<div class="articles">

    <h2>Latest Articles</h2>

    <div class="article">

        <img src="https://petshopindonesia.com/wp-content/uploads/2025/10/pexels-pixabay-45170-820x500.jpg">

        <div class="article-text">

            <h4>Cara Merawat Kucing Agar Tetap Sehat</h4>

            <p>
                Pelajari tips dasar menjaga kesehatan kucing mulai dari nutrisi,
                kebersihan hingga jadwal pemeriksaan rutin.
            </p>

            <a href="https://petshopindonesia.com/blog/"
               target="_blank">
               Read More →
            </a>

        </div>

    </div>

    <div class="article">

        <img src="data:image/jpeg;base64,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">

        <div class="article-text">

            <h4>Memilih Makanan Terbaik Untuk Anjing</h4>

            <p>
                Panduan memilih makanan yang sesuai dengan usia,
                ukuran dan kebutuhan nutrisi anjing peliharaan.
            </p>

            <a href="https://petshopindonesia.com/blog/"
               target="_blank">
               Read More →
            </a>

        </div>

    </div>

    <div class="article">

        <img src="data:image/jpeg;base64,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">

        <div class="article-text">

            <h4>Tips Grooming Hewan Peliharaan</h4>

            <p>
                Ketahui cara menjaga kebersihan bulu dan kulit hewan
                agar tetap sehat dan nyaman.
            </p>

            <a href="https://petshopindonesia.com/blog/"
               target="_blank">
               Read More →
            </a>

        </div>

    </div>

</div>

<!-- navbar -->
<div class="navbar">

    <a href="dashboard.php" class="nav-item active">
        <span class="icon">🏠</span>
        Home
    </a>

    <a href="shop.php" class="nav-item">
        <span class="icon">🛒</span>
        Explore
    </a>

    <a href="basket.php" class="nav-item">
        <span class="icon">👜</span>
        Basket
    </a>

    <a href="favorite.php" class="nav-item">
        <span class="icon">❤️</span>
        Favorite
    </a>

    <a href="profile.php" class="nav-item">
        <span class="icon">👤</span>
        Profile
    </a>

</div>

</body>
</html>