<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user'];
$date = '';
$pet = '';
$symptom = '';
$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = trim($_POST['date'] ?? '');
    $pet = trim($_POST['pet'] ?? '');
    $symptom = trim($_POST['symptom'] ?? '');

    if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $errors[] = 'Pilih tanggal janji temu yang valid.';
    } else {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            $errors[] = 'Tanggal tidak valid.';
        } elseif ($timestamp < strtotime('today')) {
            $errors[] = 'Tanggal harus hari ini atau di masa depan.';
        }
    }

    if ($pet === '') {
        $errors[] = 'Nama hewan peliharaan wajib diisi.';
    } elseif (mb_strlen($pet) > 100) {
        $errors[] = 'Nama hewan maksimal 100 karakter.';
    }

    if ($symptom === '') {
        $errors[] = 'Keluhan sakit wajib diisi.';
    } elseif (mb_strlen($symptom) > 500) {
        $errors[] = 'Keluhan maksimal 500 karakter.';
    }

    if (empty($errors)) {
        if (!isset($_SESSION['appointments'])) {
            $_SESSION['appointments'] = [];
        }
        $_SESSION['appointments'][] = [
            'date' => $date,
            'pet' => $pet,
            'symptom' => $symptom,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $message = 'Appointment berhasil dikirim!';
        $date = $pet = $symptom = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Appointment</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#d9e4dd;
    max-width:420px;
    margin:auto;
    min-height:100vh;
    padding:20px 15px;
}

h1{
    text-align:center;
    color:#5f433a;
    font-size:32px;
    font-weight:700;
    margin-bottom:15px;
}

/* CARD DOCTOR */

.card{
    background:#f7f7f7;
    border:1.5px solid #a77f70;
    border-radius:18px;
    padding:15px;
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:20px;
}

.avatar{
    width:85px;
    height:85px;
    border-radius:50%;
    object-fit:cover;
    background:#ddd;
}

.info{
    flex:1;
}

.info b{
    font-size:18px;
    color:#3e302c;
}

.info p{
    font-size:13px;
    color:#666;
    margin-top:3px;
}

.rating{
    margin-top:5px;
    color:#f4b400;
    font-size:18px;
}

.rating span{
    color:#444;
    font-size:13px;
}

/* ERROR */

.error-box{
    background:#ffe3e3;
    border:1px solid #ffb3b3;
    color:#c62828;
    padding:12px;
    border-radius:12px;
    margin-bottom:15px;
}

.error-box ul{
    padding-left:20px;
}

/* LABEL */

label{
    display:block;
    color:#5f433a;
    font-size:18px;
    font-weight:700;
    margin-bottom:6px;
    margin-top:12px;
}

/* INPUT */

input,
textarea{
    width:100%;
    border:1px solid #a77f70;
    border-radius:15px;
    background:#f8f8f8;
    padding:12px;
    font-size:14px;
    outline:none;
}

input:focus,
textarea:focus{
    border-color:#8d6257;
}

textarea{
    height:120px;
    resize:none;
}

/* BUTTON */

.submit-btn{
    width:80%;
    margin:25px auto 10px;
    display:block;
    border:none;
    background:#9b7467;
    color:white;
    font-size:16px;
    font-weight:600;
    padding:14px;
    border-radius:30px;
    cursor:pointer;
}

.submit-btn:hover{
    background:#876356;
}

/* BACK */

.back-btn{
    display:block;
    text-align:center;
    text-decoration:none;
    color:#5f433a;
    margin-top:10px;
    font-weight:600;
}

/* SUCCESS */

.msg{
    margin-top:15px;
    background:#d4edda;
    color:#155724;
    border:1px solid #c3e6cb;
    padding:12px;
    border-radius:12px;
    text-align:center;
}
    .calendar-box{
    margin-top:15px;
}

.calendar-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
    color:#5f433a;
    font-weight:700;
    font-size:16px;
}

.weekdays{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    text-align:center;
    margin-bottom:8px;
    color:#5f433a;
    font-weight:700;
}

.calendar-days{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:6px;
}

.day{
    width:36px;
    height:36px;
    margin:auto;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    color:#5f433a;
    font-weight:600;
}

.selected-date-info{
    margin-top:12px;
    padding:10px 12px;
    border-radius:14px;
    background:#f0ebe3;
    color:#5f433a;
    font-size:13px;
}

.day:hover{
    background:#d4c0b7;
}

.day.active{
    background:#7d5b4f;
    color:white;
}

.day.disabled{
    opacity:.3;
    pointer-events:none;
}
</style>

</head>
<body>

<h1>Doctor Appointment</h1>

<div class="card">

    <img
        src="doctor.png"
        class="avatar"
        alt="Doctor">

    <div class="info">

        <b>Drh. Evar Fathurohman</b>

        <p>UPTD Puskeswan Karawang</p>

        <div class="rating">
            ★★★★★ <span>4.5</span>
        </div>

    </div>

</div>

<?php if (!empty($errors)): ?>
    <div class="msg" style="background:#f8d7da;color:#842029;border:1px solid #f5c2c7;margin:15px 0;border-radius:15px;padding:12px;">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="POST">

    <label>Available Date / Time</label>

   <div class="calendar-box">

    <div class="calendar-header">
        <span>Available Date / Time</span>
        <span id="monthYear"></span>
    </div>

    <div class="weekdays">
        <div>Su</div>
        <div>Mo</div>
        <div>Tu</div>
        <div>We</div>
        <div>Th</div>
        <div>Fr</div>
        <div>Sa</div>
    </div>

    <div class="calendar-days" id="calendarDays"></div>
    <div class="selected-date-info" id="selectedDateInfo"></div>

    <input type="hidden" name="date" id="selectedDate" value="<?= htmlspecialchars($date) ?>" required>

    <label>Pet</label>
    <input
        type="text"
        name="pet"
        placeholder="Type here"
        value="<?= htmlspecialchars($pet) ?>"
        required>

    <label>Symptom</label>
    <textarea
        name="symptom"
        placeholder="Type here"
        required><?= htmlspecialchars($symptom) ?></textarea>

    <button
        type="submit"
        class="submit-btn">
        Submit
    </button>

</form>

<a href="dashboard.php" class="back-btn">
    ← Back
</a>

<?php if ($message): ?>
<div class="msg"><?= $message ?></div>
<?php endif; ?>
<script>
const calendarDays = document.getElementById('calendarDays');
const monthYear = document.getElementById('monthYear');
const selectedDate = document.getElementById('selectedDate');
const selectedDateInfo = document.getElementById('selectedDateInfo');

const today = new Date();

const year = today.getFullYear();
const month = today.getMonth();

monthYear.textContent =
new Intl.DateTimeFormat('en', {
    month:'short',
    year:'numeric'
}).format(today);

function updateSelectedDateInfo(value) {
    if (!value) {
        selectedDateInfo.textContent = 'Pilih tanggal di atas untuk membuat janji.';
        return;
    }
    const dateText = new Intl.DateTimeFormat('id', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(new Date(value));
    selectedDateInfo.textContent = 'Tanggal terpilih: ' + dateText;
}

updateSelectedDateInfo(selectedDate.value);

const firstDay =
new Date(year, month, 1).getDay();

const lastDate =
new Date(year, month + 1, 0).getDate();

for(let i=0;i<firstDay;i++){
    const blank=document.createElement('div');
    calendarDays.appendChild(blank);
}

for(let day=1;day<=lastDate;day++){

    const dateBtn=document.createElement('div');

    dateBtn.classList.add('day');
    dateBtn.textContent=day;

    const currentDate =
    new Date(year,month,day);

    if(currentDate < new Date().setHours(0,0,0,0)){
        dateBtn.classList.add('disabled');
    }

    const selectedValue = selectedDate.value;
    if (selectedValue) {
        const [selY, selM, selD] = selectedValue.split('-').map(Number);
        if (selY === year && selM === month + 1 && selD === day) {
            dateBtn.classList.add('active');
        }
    }

    dateBtn.onclick = () => {
        document
            .querySelectorAll('.day')
            .forEach(d => d.classList.remove('active'));

        dateBtn.classList.add('active');

        const mm = String(month + 1).padStart(2, '0');
        const dd = String(day).padStart(2, '0');

        selectedDate.value = `${year}-${mm}-${dd}`;
        updateSelectedDateInfo(selectedDate.value);
    };

    calendarDays.appendChild(dateBtn);
}
</script>
</body>
</html>