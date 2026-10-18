<?php
session_start();
require 'db.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$usernameValue = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usernameValue = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usernameValue === '' || $password === '') {
        $error = "Username dan password wajib diisi.";
    } else {
        $username = $usernameValue;
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");

        if(!$stmt){
            die("Query Error: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            // password hash verify
            if (password_verify($password, $row['password'])) {

                $_SESSION['user'] = $row['username'];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Password salah!";
            }

        } else {

            $error = "Username tidak ditemukan!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pawfect Pet Care</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;

    background-image:url("assets/Screenshot 2026-05-03 113007.png");
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;

    padding:20px;
}

/* container */
.container{
    width:100%;
    max-width:420px;
}

/* logo */
.logo{
    text-align:center;
    margin-bottom:10px;
}

.logo img{
    width:180px;
}

/* title */
h1{
    text-align:center;
    font-size:48px;
    color:#6e4b3a;
    margin-bottom:10px;
}

.subtitle{
    text-align:center;
    color:#6e4b3a;
    font-size:13px;
    font-weight:600;
    margin-bottom:20px;
}

/* card */
.card{
    background:#e9d2b7;
    border:2px solid #b88c6b;
    border-radius:25px;
    padding:30px 20px;
    box-shadow:0 5px 0 #9c765d;
}

/* title card */
.card h3{
    text-align:center;
    margin-bottom:20px;
    color:#6e4b3a;
    font-size:20px;
}

/* message */
.msg{
    background:#ffdede;
    color:#a10000;
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
    text-align:center;
    font-size:13px;
}

/* input */
input[type="text"],
input[type="password"]{
    width:100%;
    height:50px;
    border-radius:30px;
    border:2px solid #d8a7a0;
    padding:0 18px;
    outline:none;
    background:#f8efe4;
    font-size:14px;
    margin-bottom:15px;
}

/* options */
.options{
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:12px;
    margin-bottom:20px;
    color:#6e4b3a;
}

.options a{
    text-decoration:none;
    color:#6e4b3a;
    font-weight:600;
}

/* button */
.login-btn{
    width:100%;
    height:50px;
    border:none;
    border-radius:30px;
    background:#8d6257;
    color:white;
    font-weight:bold;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}

.login-btn:hover{
    opacity:.9;
}

/* divider */
.divider{
    margin:20px 0;
    text-align:center;
    font-size:12px;
    color:#6e4b3a;
    position:relative;
}

.divider::before,
.divider::after{
    content:"";
    position:absolute;
    width:30%;
    height:1px;
    background:#6e4b3a;
    top:50%;
}

.divider::before{
    left:0;
}

.divider::after{
    right:0;
}

/* google */
.google{
    text-align:center;
}

.google img{
    width:45px;
}

/* signup */
.signup{
    margin-top:20px;
    text-align:center;
    font-size:12px;
    color:#6e4b3a;
}

.signup a{
    color:#6e4b3a;
    font-weight:bold;
    text-decoration:none;
}

/* responsive */
@media(max-width:480px){

    h1{
        font-size:40px;
    }

    .card{
        padding:25px 15px;
    }

    .logo img{
        width:150px;
    }

    input{
        height:45px;
        font-size:13px;
    }

    .login-btn{
        height:45px;
        font-size:14px;
    }
}

</style>
</head>

<body>

<div class="container">

    <!-- logo -->
    <div class="logo">
        <img src="assets/logo1-removebg-preview.png" alt="Logo">
    </div>

    <!-- title -->
    <h1>Welcome!</h1>

    <div class="subtitle">
        Log in and enjoy all Pawfect features
    </div>

    <!-- card -->
    <div class="card">

        <h3>Log In To Your Account</h3>

        <?php if (!empty($error)) : ?>
            <div class="msg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- form -->
        <form method="POST">

            <input
                type="text"
                name="username"
                placeholder="Username"
                value="<?= htmlspecialchars($usernameValue) ?>"
                autocomplete="username"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                autocomplete="current-password"
                required
            >

            <div class="options">

                <label>
                    <input type="checkbox">
                    Remember Me
                </label>

                <a href="lupapass.php">
                    Forgot Password?
                </a>

            </div>

            <button type="submit" class="login-btn">
                Log In
            </button>

        </form>

        <!-- divider -->
        <div class="divider">
            Or Log In with
        </div>

        <!-- google -->
        <div class="google">
            <img src="https://www.transparentpng.com/thumb/google-logo/google-logo-png-icon-free-download-SUF63j.png" alt="Google">
        </div>

        <!-- signup -->
        <div class="signup">
            Don’t have an account?
            <a href="regist.php">Sign Up</a>
        </div>

    </div>

</div>

</body>
</html>