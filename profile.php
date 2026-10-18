<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$username = $_SESSION['user'];
$email = '-';
$nama = '-';

$stmt = $conn->prepare('SELECT nama, email FROM user WHERE username = ?');
if ($stmt) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nama = $row['nama'] ?: '-';
        $email = $row['email'] ?: '-';
    }
    $stmt->close();
}
$pets = [];

$petQuery = $conn->prepare("
SELECT * FROM pets
WHERE username = ?
LIMIT 5
");

$petQuery->bind_param('s',$username);
$petQuery->execute();

$result = $petQuery->get_result();

while($row = $result->fetch_assoc()){
    $pets[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Pawfect</title>
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
    padding-bottom:90px;
}

.container{
    padding:15px;
}

.title{
    text-align:center;
    font-size:42px;
    font-weight:700;
    margin:30px 0 15px;
}

.profile-card{
    background:#f1e8df;
    border:1px solid #9d7c6f;
    border-radius:18px;
    padding:12px;
    display:flex;
    align-items:center;
    gap:12px;
}

.avatar{
    width:75px;
    height:75px;
    border-radius:50%;
    overflow:hidden;
    border:4px solid #d6e8dd;
}

.avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.profile-info{
    flex:1;
}

.profile-info h3{
    font-size:22px;
    margin-bottom:3px;
}

.profile-info p{
    font-size:14px;
}

.member{
    display:inline-block;
    margin-top:6px;
    background:#7b5144;
    color:white;
    padding:4px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.pet-title{
    margin-top:18px;
    margin-bottom:10px;
    font-size:24px;
    font-weight:700;
}

.pet-grid{
    display:flex;
    gap:10px;
    overflow-x:auto;
}

.pet-card{
    min-width:115px;
    background:#f1e8df;
    border:1px solid #9d7c6f;
    border-radius:16px;
    text-align:center;
    padding:10px;
}

.pet-card img{
    width:65px;
    height:65px;
    border-radius:50%;
    object-fit:cover;
    margin-bottom:8px;
}

.pet-name{
    font-weight:700;
}

.pet-type{
    font-size:12px;
}

.pet-age{
    font-size:12px;
}

.add-pet{
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:50px;
    color:#6b4a3f;
}

.menu{
    margin-top:20px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    text-decoration:none;
    color:#3d3026;
    font-weight:600;
    padding:12px 0;
    border-bottom:1px solid #999;
}

.menu-icon{
    font-size:24px;
    width:30px;
    text-align:center;
}

.logout{
    display:block;
    width:100%;
    margin-top:25px;
    text-align:center;
    background:#9a6f60;
    color:white;
    text-decoration:none;
    padding:15px;
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
</style>
</head>
<body>
<div class="container">

    <div class="title">
        Profile
    </div>

    <div class="profile-card">

        <div class="avatar">
            <img src="images/user.png">
        </div>

        <div class="profile-info">
            <h3>Hi, <?= htmlspecialchars($nama) ?></h3>
            <p><?= htmlspecialchars($email) ?></p>

            <div class="member">
                🏅 Premium Member
            </div>
        </div>

    </div>

    <div class="pet-title">
        MY PETS
    </div>

    <div class="pet-grid">

        <?php foreach($pets as $pet): ?>

        <div class="pet-card">

            <img src="<?= htmlspecialchars($pet['foto']) ?>">

            <div class="pet-name">
                <?= htmlspecialchars($pet['nama_pet']) ?>
            </div>

            <div class="pet-type">
                <?= htmlspecialchars($pet['jenis_pet']) ?>
            </div>

            <div class="pet-age">
                Age <?= htmlspecialchars($pet['umur']) ?>
            </div>

        </div>

        <?php endforeach; ?>

        <div class="pet-card add-pet">
            <a href="add_pet.php" style="text-decoration:none;color:#6b4a3f;">+</a>
        </div>

    </div>

    <div class="menu">

        <a href="edit_profile.php">
            <span class="menu-icon">📝</span>
            Edit Profile
        </a>

        <a href="orders.php">
            <span class="menu-icon">📦</span>
            Order & Service History
        </a>

        <a href="maintenance.php">
            <span class="menu-icon">🎁</span>
            Rewards & Points
        </a>

        <a href="security.php">
            <span class="menu-icon">🔔</span>
            Notifications & Security
        </a>

        <a href="help.php">
            <span class="menu-icon">❓</span>
            Help & Support / FAQ
        </a>

    </div>

    <a href="logout.php" class="logout">
        Log Out
    </a>

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
