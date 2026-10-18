<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Maintenance - Pawfect</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:#d7e1da;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
    position:relative;
}

/* Dekorasi Header */
.header-decoration{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    z-index:1;
}

/* Dekorasi Footer */
.footer-decoration{
    position:absolute;
    bottom:0;
    left:0;
    width:100%;
    z-index:1;
}

.container{
    text-align:center;
    padding:20px;
    max-width:500px;
    width:100%;
    position:relative;
    z-index:2;
}

.title{
    font-size:36px;
    font-weight:700;
    color:#5f433a;
    margin-bottom:20px;
}

.cat-image{
    width:260px;
    max-width:80%;
    margin-bottom:20px;
    animation: float 3s ease-in-out infinite;
}

@keyframes float{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-10px);
    }
}

.subtitle{
    color:#6e5b53;
    font-size:18px;
    line-height:1.8;
    margin-bottom:30px;
}

.btn{
    background:#6f5448;
    color:white;
    border:none;
    padding:14px 35px;
    border-radius:50px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

.btn:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.footer-text{
    margin-top:20px;
    font-size:13px;
    color:#8b7d76;
}

@media(max-width:768px){

    .title{
        font-size:28px;
    }

    .subtitle{
        font-size:15px;
    }

    .cat-image{
        width:200px;
    }

}

</style>
</head>

<body>

<div class="container">

    <h1 class="title">
        Under Maintenance !
    </h1>

    <!-- Ganti dengan gambar kucing -->
    <img src="assets/maintenance.png" class="cat-image" alt="Maintenance">

    <p class="subtitle">
        Sorry, The Application is under maintenance.<br>
        We'll be back soon
    </p>

    <div class="footer-text">
        © 2026 Pawfect. All Rights Reserved.
    </div>

</div>
</body>
</html>