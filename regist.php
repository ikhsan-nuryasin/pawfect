<?php
session_start();
require 'db.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";
$usernameValue = "";
$emailValue = "";
$namaValue = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $nama     = trim($_POST['nama'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    $usernameValue = $username;
    $emailValue = $email;
    $namaValue = $nama;

    // VALIDASI
    if (empty($username) || empty($email) || empty($nama) || empty($password) || empty($confirm)) {
        $message = "Semua field wajib diisi!";

    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){

        $message = "Format email tidak valid!";

    } elseif(strlen($password) < 8){

        $message = "Password minimal 8 karakter!";

    } elseif($password !== $confirm){

        $message = "Konfirmasi password tidak cocok!";

    } else {

        // cek user/email
        $check = $conn->prepare("SELECT id FROM user WHERE username=? OR email=?");

        if(!$check){
            die("Query Error: " . $conn->error);
        }

        $check->bind_param("ss", $username, $email);
        $check->execute();

        $result = $check->get_result();

        if($result->num_rows > 0){

            $message = "Username atau email sudah digunakan!";

        } else {

            // HASH PASSWORD
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // insert user
            $stmt = $conn->prepare("
                INSERT INTO user(username,email,password,nama)
                VALUES(?,?,?,?)
            ");

            if (!$stmt) {
                die("Insert Error: " . $conn->error);
            }

            $stmt->bind_param("ssss", $username, $email, $hash, $nama);

            if($stmt->execute()){

                $_SESSION['user'] = $username;

                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Register gagal!";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register - Pawfect Pet Care</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
    padding:20px;

    background-image:url("assets/Screenshot 2026-05-03 113007.png");
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
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
.title{
    text-align:center;
    margin-bottom:20px;
}

.title h1{
    font-size:48px;
    color:#6b4f3a;
    line-height:1;
}

.title p{
    margin-top:10px;
    font-size:13px;
    color:#6b4f3a;
    font-weight:600;
}

/* card */
.card{
    background:#e9d2b7;
    border:2px solid #b88c6b;
    border-radius:25px;
    padding:30px 20px;
    box-shadow:0 6px 0 #9c765d;
}

/* heading */
.card h2{
    text-align:center;
    color:#6b4f3a;
    margin-bottom:20px;
}

/* error */
.message{
    background:#ffe3e3;
    color:#b00020;
    padding:12px;
    border-radius:12px;
    margin-bottom:15px;
    text-align:center;
    font-size:13px;
}

/* input group */
.input-group{
    margin-bottom:15px;
    position:relative;
}

.input-group input{
    width:100%;
    height:50px;
    border:none;
    outline:none;
    border-radius:30px;
    background:#f8efe4;
    border:2px solid #d9b09a;
    padding:0 18px;
    font-size:14px;
}

/* eye */
.eye{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    user-select:none;
}

/* hint */
.hint{
    margin-top:5px;
    margin-left:10px;
    font-size:10px;
    color:#6b4f3a;
}

/* button */
.btn{
    width:100%;
    height:50px;
    border:none;
    border-radius:30px;
    background:#8d6257;
    color:white;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
    margin-top:5px;
}

.btn:hover{
    opacity:.9;
    transform:scale(1.01);
}

/* login */
.login{
    text-align:center;
    margin-top:18px;
    font-size:12px;
    color:#6b4f3a;
}

.login a{
    color:#7d5c50;
    font-weight:700;
    text-decoration:none;
}

/* responsive */
@media(max-width:480px){

    .container{
        max-width:100%;
    }

    .logo img{
        width:150px;
    }

    .title h1{
        font-size:40px;
    }

    .title p{
        font-size:12px;
    }

    .card{
        padding:25px 15px;
    }

    .card h2{
        font-size:20px;
    }

    .input-group input{
        height:46px;
        font-size:13px;
    }

    .btn{
        height:46px;
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
    <div class="title">

        <h1>Welcome!</h1>

        <p>
            Create your account and enjoy all Pawfect features
        </p>

    </div>

    <!-- card -->
    <div class="card">

        <h2>Create Account</h2>

        <?php if(!empty($message)) : ?>

            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <!-- form -->
        <form method="POST">

            <!-- username -->
            <div class="input-group">

                <input
                    type="text"
                    name="username"
                    placeholder="Your Username"
                    value="<?= htmlspecialchars($usernameValue) ?>"
                    autocomplete="username"
                    required
                >

            </div>

            <div class="input-group">

                <input
                    type="text"
                    name="nama"
                    placeholder="Your Name"
                    value="<?= htmlspecialchars($namaValue) ?>"
                    autocomplete="name"
                    required
                >

            </div>

            <!-- email -->
            <div class="input-group">

                <input
                    type="email"
                    name="email"
                    placeholder="Your Email"
                    value="<?= htmlspecialchars($emailValue) ?>"
                    autocomplete="email"
                    required
                >

            </div>

            <!-- password -->
            <div class="input-group">

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Create Password"
                    required
                >

                <span class="eye" onclick="togglePassword('password')">
                    👁️
                </span>

                <div class="hint">
                    Password minimal 8 karakter
                </div>

            </div>

            <!-- confirm -->
            <div class="input-group">

                <input
                    type="password"
                    name="confirm"
                    id="confirm"
                    placeholder="Confirm Password"
                    required
                >

                <span class="eye" onclick="togglePassword('confirm')">
                    👁️
                </span>

            </div>

            <!-- button -->
            <button type="submit" class="btn">
                Create Account
            </button>

        </form>

        <!-- login -->
        <div class="login">

            Already have an account?
            <a href="login.php">
                Log In
            </a>

        </div>

    </div>

</div>

<script>

function togglePassword(id){

    const input = document.getElementById(id);

    if(input.type === "password"){

        input.type = "text";

    } else {

        input.type = "password";
    }
}

</script>

</body>
</html>