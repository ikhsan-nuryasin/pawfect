<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & FAQ - Pawfect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #d7e2db;
            max-width: 430px;
            margin: auto;
            min-height: 100vh;
            padding-bottom: 100px;
            color: #5f433a;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .faq-item {
            background: #f1e8df;
            border: 1px solid #9d7c6f;
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .faq-question {
            padding: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #5f433a;
            user-select: none;
            transition: background 0.3s;
        }

        .faq-question:hover {
            background: #e8dfd6;
        }

        .faq-toggle {
            font-size: 20px;
            transition: transform 0.3s;
        }

        .faq-toggle.open {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 15px;
        }

        .faq-answer.open {
            max-height: 500px;
            padding: 0 15px 15px 15px;
        }

        .faq-answer p {
            line-height: 1.6;
            color: #8b7355;
            font-size: 14px;
        }

        .category-title {
            font-size: 18px;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
            color: #5f433a;
        }

        .contact-section {
            background: #f1e8df;
            border: 1px solid #9d7c6f;
            border-radius: 15px;
            padding: 20px;
            margin-top: 25px;
            text-align: center;
        }

        .contact-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .contact-title {
            font-weight: 700;
            margin-bottom: 10px;
            color: #5f433a;
        }

        .contact-info {
            font-size: 13px;
            color: #8b7355;
            line-height: 1.8;
        }

        .contact-link {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 20px;
            background: #9a6f60;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 430px;
            width: 100%;
            margin: auto;
            background: #f7efe9;
            border-radius: 20px 20px 0 0;
            padding: 10px 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -4px 12px rgba(0,0,0,.08);
            border: 1px solid rgba(0,0,0,.05);
            z-index: 999;
        }

        .navbar a {
            text-decoration: none;
            color: #5f433a;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 4px 0;
        }

        .navbar a .icon {
            font-size: 22px;
            display: block;
        }

        .navbar a.active {
            color: #8b5b41;
        }

        .navbar a.active .icon {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❓ Help & FAQ</h1>
        </div>

        <div class="category-title">🛍️ Tentang Belanja</div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>Bagaimana cara membeli produk?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Untuk membeli produk, ikuti langkah berikut: 1) Pilih kategori produk, 2) Cari produk yang diinginkan, 3) Klik produk dan baca deskripsi, 4) Klik "Tambah ke Keranjang", 5) Lanjutkan ke checkout, 6) Isi data pengiriman dan pembayaran.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>Berapa lama waktu pengiriman?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Waktu pengiriman tergantung lokasi Anda. Untuk area Jabodetabek, pengiriman biasanya 1-2 hari kerja. Untuk area luar Jabodetabek, 2-5 hari kerja. Anda akan menerima notifikasi saat paket dikirim.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>Metode pembayaran apa saja yang tersedia?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Kami menerima berbagai metode pembayaran: Transfer Bank, e-Wallet (GCash, GoPay, OVO, DANA), Kartu Kredit/Debit, dan Cash on Delivery (COD).</p>
            </div>
        </div>

        <div class="category-title">📦 Tentang Produk</div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>Apakah produk original?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Ya, semua produk yang kami jual adalah original dan bergaransi resmi. Kami bekerja sama langsung dengan distributor resmi untuk memastikan kualitas produk.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>Bagaimana jika produk rusak saat pengiriman?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Jika produk rusak atau tidak sesuai dengan deskripsi, hubungi customer service kami dalam 24 jam setelah penerimaan. Kami siap membantu pengembalian atau penggantian produk tanpa biaya tambahan.</p>
            </div>
        </div>

        <div class="category-title">🔄 Tentang Pengembalian</div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>Bagaimana proses return/pengembalian?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Silakan hubungi customer service kami terlebih dahulu untuk meminta approval return. Setelah disetujui, kembalikan produk dalam kondisi original. Biaya return akan kami tanggung untuk alasan cacat pabrik atau kesalahan kami.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>Berapa waktu untuk mendapat refund?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Setelah kami menerima produk yang dikembalikan dan melakukan pengecekan, refund akan diproses dalam 3-7 hari kerja ke rekening Anda.</p>
            </div>
        </div>

        <div class="contact-section">
            <div class="contact-icon">💬</div>
            <div class="contact-title">Butuh Bantuan Lebih Lanjut?</div>
            <div class="contact-info">
                Hubungi tim customer service kami yang siap membantu 24/7<br><br>
                📧 Email: support@pawfect.com<br>
                💬 WhatsApp: +62 821 1234 5678<br>
                📞 Telepon: 021-1234-5678
            </div>
            <a href="mailto:support@pawfect.com" class="contact-link">Kirim Email</a>
        </div>
    </div>

    <div class="navbar">
        <a href="dashboard.php" class="nav-item"><span class="icon">🏠</span>Home</a>
        <a href="shop.php" class="nav-item"><span class="icon">🛒</span>Explore</a>
        <a href="basket.php" class="nav-item"><span class="icon">👜</span>Basket</a>
        <a href="favorite.php" class="nav-item"><span class="icon">❤️</span>Favorite</a>
        <a href="profile.php" class="nav-item active"><span class="icon">👤</span>Profile</a>
    </div>

    <script>
        function toggleFAQ(element) {
            const question = element;
            const answer = element.nextElementSibling;
            const toggle = question.querySelector('.faq-toggle');

            answer.classList.toggle('open');
            toggle.classList.toggle('open');
        }
    </script>
</body>
</html>
