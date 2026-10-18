<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['user'];

function currentChatTime(): string
{
    return date('d/m/Y H:i');
}

function getBotReply(string $message): string
{
    $text = trim(strtolower($message));

    $replies = [
        'hi' => 'Halo! Saya asisten Pawfect. Ada yang bisa saya bantu hari ini?',
        'halo' => 'Halo! Saya asisten Pawfect. Silakan tulis pertanyaan Anda.',
        'selamat pagi' => 'Selamat pagi! Apakah Anda ingin melihat layanan grooming atau produk perawatan hewan?',
        'selamat siang' => 'Selamat siang! Apakah Anda butuh bantuan memilih makanan atau layanan perawatan?',
        'selamat malam' => 'Selamat malam! Saya siap membantu Anda kapan saja.',
        'kucing' => 'Kami menyediakan layanan perawatan kucing, makanan, vaksin, dan grooming. Ingin lihat paket perawatan kucing?',
        'anjing' => 'Kami punya layanan untuk anjing juga, seperti perawatan, makanan, vaksin, dan boarding. Mau saya bantu cari paket terbaik?',
        'grooming' => 'Layanan grooming kami mencakup mandi, potong bulu, dan perawatan kuku. Mau jadwalkan grooming sekarang?',
        'boarding' => 'Kami menyediakan boarding harian untuk hewan peliharaan dengan fasilitas nyaman. Mau tahu tarifnya?',
        'dokter' => 'Kami bisa membantu Anda menghubungi dokter hewan. Apa keluhan hewan peliharaan Anda?',
        'vaksin' => 'Vaksinasi tersedia untuk kucing dan anjing. Apakah Anda ingin mengatur jadwal vaksinasi?',
        'obat' => 'Untuk obat dan suplemen, sebutkan apa yang Anda cari—misalnya kutu, cacing, atau nutrisi.',
        'makanan' => 'Kami punya makanan khusus untuk kucing dan anjing. Mau rekomendasi makanan sesuai usia hewan peliharaan?',
        'promo' => 'Saat ini ada promo untuk paket grooming dan boarding. Mau tahu detailnya?',
        'diskon' => 'Diskon tersedia untuk pembelian paket grooming dan produk perawatan. Sebutkan jenis layanan yang Anda butuhkan.',
        'harga' => 'Untuk informasi harga, sebutkan layanan yang Anda cari: grooming, produk, boarding, atau vaksinasi.',
        'booking' => 'Anda bisa melakukan booking melalui dashboard kami. Mau saya arahkan ke halaman layanan atau bantu langsung di sini?',
        'order' => 'Untuk status pesanan, silakan sebutkan nomor pesanan atau akses halaman “Orders” di dashboard.',
        'bantuan' => 'Saya siap membantu. Sebutkan apakah Anda butuh layanan, produk, atau bantuan teknis.',
        'kontak' => 'Anda bisa menghubungi kami lewat dashboard atau pesan ini. Perlu nomor telepon/cs?',
        'faq' => 'Silakan tanyakan apa saja tentang layanan kami: grooming, boarding, obat, atau produk hewan peliharaan.',
        'terima kasih' => 'Sama-sama! Jika ada pertanyaan lain, silakan kirim pesan lagi.',
        'thank you' => 'Sama-sama! Jika ada pertanyaan lain, silakan kirim pesan lagi.',
        'bye' => 'Terima kasih sudah menggunakan Pawfect. Semoga hari Anda menyenangkan!',
    ];

    foreach ($replies as $keyword => $reply) {
        if (str_contains($text, $keyword)) {
            return $reply;
        }
    }

    $fallbacks = [
        'Saya akan segera membantu Anda. Silakan jelaskan lebih lanjut tentang kebutuhan hewan peliharaan Anda.',
        'Coba tanyakan tentang layanan grooming, boarding, vaksinasi, atau produk makanan hewan.',
        'Saya siap menjawab pertanyaan seputar layanan, harga, atau paket promo Pawfect.',
    ];

    return $fallbacks[array_rand($fallbacks)];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send') {
    $message = trim($_POST['message'] ?? '');
    if ($message === '') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Pesan tidak boleh kosong.']);
        exit;
    }

    if (!isset($_SESSION['chat_messages']) || !is_array($_SESSION['chat_messages'])) {
        $_SESSION['chat_messages'] = [];
    }

    $time = currentChatTime();
    $_SESSION['chat_messages'][] = [
        'type' => 'user',
        'text' => $message,
        'time' => $time,
    ];

    $replyText = getBotReply($message);
    $_SESSION['chat_messages'][] = [
        'type' => 'bot',
        'text' => $replyText,
        'time' => currentChatTime(),
    ];

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => $replyText, 'time' => currentChatTime()]);
    exit;
}

if (!isset($_SESSION['chat_messages']) || !is_array($_SESSION['chat_messages'])) {
    $_SESSION['chat_messages'] = [
        [
            'type' => 'bot',
            'text' => 'Halo, ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '! Saya asisten Pawfect. Silakan ketik pesan Anda.',
            'time' => currentChatTime(),
        ],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Pawfect</title>
    <style>
       *{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#d8e3dc;
    height:100vh;
    max-width:430px;
    margin:auto;
    overflow:hidden;
}

.chat-container{
    height:100vh;
    display:flex;
    flex-direction:column;
}

/* HEADER */

.chat-header{
    background:#6f5448;
    color:white;
    padding:12px;
    display:flex;
    align-items:center;
    gap:10px;
}

.back{
    font-size:28px;
    text-decoration:none;
    color:white;
}

.avatar{
    width:45px;
    height:45px;
    border-radius:50%;
    background:#b9b9b9;
}

.header-info{
    flex:1;
}

.header-info h3{
    font-size:18px;
}

.online{
    color:#6df56d;
    font-size:12px;
}

/* CHAT */

.chat-body{
    flex:1;
    overflow-y:auto;
    padding:15px;
}

.quick-actions{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    padding:0 15px 10px;
}

.quick-actions button{
    flex:1 1 calc(50% - 10px);
    min-width:120px;
    border:none;
    border-radius:20px;
    padding:10px 12px;
    background:#8fddb0;
    color:#2f433d;
    font-size:13px;
    cursor:pointer;
    transition:transform .1s ease, background .2s ease;
}

.quick-actions button:hover{
    transform:scale(1.03);
    background:#7cd6a3;
}

.message{
    max-width:75%;
    padding:12px 16px;
    border-radius:20px;
    margin-bottom:12px;
    position:relative;
}

.bot{
    background:white;
    border:1px solid #7b5a4c;
    color:#4d3b34;
}

.user{
    background:#8fddb0;
    margin-left:auto;
    color:#3a3a3a;
}

.time{
    display:block;
    font-size:10px;
    margin-top:4px;
    opacity:.7;
}

/* INPUT */

.chat-footer{
    padding:10px;
    display:flex;
    align-items:center;
    gap:10px;
    background:transparent;
}

.attach,
.send{
    width:45px;
    height:45px;
    border:none;
    border-radius:50%;
    background:#6f5448;
    color:white;
    font-size:20px;
}

.chat-input{
    flex:1;
    height:45px;
    border:none;
    border-radius:25px;
    padding:0 15px;
    font-size:16px;
}
    </style>
</head>
<body>
<div class="chat-container">

    <div class="chat-header">

        <a href="dashboard.php" class="back">‹</a>

        <div class="avatar"></div>

        <div class="header-info">
            <h3>Pawfect Shop Assistant</h3>
            <div class="online">● Online</div>
        </div>

        <div style="font-size:24px;">📞</div>
        <div style="font-size:24px;">⋮</div>

    </div>

    <div class="chat-body" id="chatBody">
        <?php foreach ($_SESSION['chat_messages'] as $msg): ?>
            <div class="message <?php echo $msg['type'] === 'bot' ? 'bot' : 'user'; ?>">
                <?php echo htmlspecialchars($msg['text'], ENT_QUOTES, 'UTF-8'); ?>
                <span class="time"><?php echo htmlspecialchars($msg['time'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="quick-actions">
        <button type="button" data-message="Grooming">Grooming</button>
        <button type="button" data-message="Boarding">Boarding</button>
        <button type="button" data-message="Harga">Harga</button>
        <button type="button" data-message="Promo">Promo</button>
    </div>

    <form class="chat-footer" id="chatForm">
        <button type="button" class="attach">📎</button>
        <input
            type="text"
            id="chatInput"
            name="message"
            class="chat-input"
            placeholder="Tulis pesan..."
            autocomplete="off"
            required>
        <button type="submit" class="send">➤</button>
    </form>

</div>

<script>
const chatForm = document.getElementById('chatForm');
const chatInput = document.getElementById('chatInput');
const chatBody = document.getElementById('chatBody');
const quickButtons = document.querySelectorAll('.quick-actions button');

function scrollToBottom() {
    chatBody.scrollTop = chatBody.scrollHeight;
}

function appendMessage(text, type, time) {
    const message = document.createElement('div');
    message.className = 'message ' + type;

    const textNode = document.createElement('div');
    textNode.textContent = text;
    textNode.className = 'message-text';

    const timeNode = document.createElement('span');
    timeNode.className = 'time';
    timeNode.textContent = time;

    message.appendChild(textNode);
    message.appendChild(timeNode);
    chatBody.appendChild(message);
    scrollToBottom();
}

chatForm.addEventListener('submit', async function (event) {
    event.preventDefault();

    const message = chatInput.value.trim();
    if (!message) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'send');
    formData.append('message', message);

    appendMessage(message, 'user', new Date().toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }));
    chatInput.value = '';
    chatInput.focus();

    try {
        const response = await fetch('chat.php', {
            method: 'POST',
            body: formData,
        });

        const data = await response.json();
        if (data.success) {
            appendMessage(data.message, 'bot', data.time);
        } else {
            appendMessage('Terjadi kesalahan: ' + data.error, 'bot', new Date().toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }));
        }
    } catch (error) {
        appendMessage('Tidak dapat terhubung ke server.', 'bot', new Date().toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }));
    }
});

quickButtons.forEach(button => {
    button.addEventListener('click', () => {
        const value = button.getAttribute('data-message');
        if (!value) return;
        chatInput.value = value;
        chatForm.requestSubmit();
    });
});

scrollToBottom();
</script>

</body>
</html>
