<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = "Link reset password telah dikirim!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - Pawfect Pet Care</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background-image: url("assets/Screenshot 2026-05-03 113007.png");
    background-size: cover;
    background-position: center;

    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* container */
.container {
    width: 100%;
    max-width: 380px;
    padding: 20px;
    box-sizing: border-box;
}

/* content */
.content {
    text-align: left;
}

/* title */
.title {
    font-size: 32px;
    font-weight: 800;
    color: #5a3e32;
    line-height: 1.2;
    margin-bottom: 10px;
}

/* desc */
.desc {
    font-size: 13px;
    color: #2b2b2b;
    margin-bottom: 40px;
}

/* input */
.input-group {
    margin-bottom: 25px;
}

.input-group input {
    width: 100%;
    border: none;
    border-bottom: 2px solid #5a3e32;
    background: transparent;
    padding: 10px 5px;
    font-size: 13px;
    outline: none;
}

/* button utama (disabled style) */
.btn-disabled {
    width: 100%;
    height: 45px;
    border-radius: 30px;
    border: none;
    background: #cbb7b0;
    color: white;
    font-weight: bold;
    margin-bottom: 15px;
}

/* button back */
.btn-back {
    width: 100%;
    height: 48px;
    border-radius: 30px;
    border: none;
    background: #8c5a3c;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

/* signup */
.signup {
    text-align: center;
    font-size: 12px;
    margin-top: 15px;
}

.signup a {
    color: #5a6cff;
    text-decoration: none;
    font-weight: bold;
}

/* message */
.msg {
    font-size: 12px;
    color: green;
    margin-bottom: 15px;
}
</style>

</head>
<body>

<div class="container">
    <div class="content">

        <div class="title">
            I forgot<br>My Password
        </div>

        <div class="desc">
            Enter your email, phone, or username<br>
            and we'll send you a link to change a new password
        </div>

        <?php if (!empty($message)) : ?>
            <div class="msg"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="input-group">
                <input type="text" placeholder="Username, Email or Phone Number" required>
            </div>

            <button class="btn-disabled" disabled>Forgot Password</button>

            <button type="button" class="btn-back" onclick="history.back()">Back</button>

        </form>

        <div class="signup">
            Don't have an account ? <a href="regist.php">SignUp</a>
        </div>

    </div>
</div>

</body>
</html>