<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user'];
$pet = '';
$type = 'Grooming';
$date_in = '';
$date_out = '';
$notes = '';
$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet = trim($_POST['pet'] ?? '');
    $type = trim($_POST['type'] ?? 'Grooming');
    $date_in = trim($_POST['date_in'] ?? '');
    $date_out = trim($_POST['date_out'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($pet === '') {
        $errors[] = 'Nama hewan wajib diisi.';
    } elseif (mb_strlen($pet) > 100) {
        $errors[] = 'Nama hewan maksimal 100 karakter.';
    }

    $validTypes = ['Grooming', 'Boarding'];
    if (!in_array($type, $validTypes, true)) {
        $type = 'Grooming';
    }

    if ($date_in === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_in)) {
        $errors[] = 'Tanggal masuk wajib diisi dengan format yang benar.';
    }
    if ($date_out === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_out)) {
        $errors[] = 'Tanggal keluar wajib diisi dengan format yang benar.';
    }

    if (empty($errors)) {
        $start = strtotime($date_in);
        $end = strtotime($date_out);
        if ($start === false || $end === false) {
            $errors[] = 'Tanggal tidak valid.';
        } elseif ($start < strtotime('today')) {
            $errors[] = 'Tanggal masuk harus hari ini atau setelahnya.';
        } elseif ($end < $start) {
            $errors[] = 'Tanggal keluar harus sama atau setelah tanggal masuk.';
        }
    }

    if (mb_strlen($notes) > 500) {
        $errors[] = 'Catatan maksimal 500 karakter.';
    }

    if (empty($errors)) {
        if (!isset($_SESSION['bookings'])) {
            $_SESSION['bookings'] = [];
        }
        $_SESSION['bookings'][] = [
            'pet' => $pet,
            'type' => $type,
            'date_in' => $date_in,
            'date_out' => $date_out,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $message = 'Booking berhasil dikirim!';
        $pet = '';
        $type = 'Grooming';
        $date_in = '';
        $date_out = '';
        $notes = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pet Boarding</title>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: sans-serif;
}

body {
  background: #cfded7;
  padding: 15px;
}

/* TITLE */
h1 {
  text-align: center;
  color: #5a3e36;
  margin-bottom: 15px;
}

/* LABEL */
label {
  display: block;
  margin-top: 10px;
  color: #5a3e36;
  font-weight: bold;
}

/* INPUT */
input, select, textarea {
  width: 100%;
  padding: 12px;
  border-radius: 15px;
  border: 1px solid #a67c6b;
  margin-top: 5px;
  font-size: 14px;
}

/* DATE GROUP */
.date-group {
  display: flex;
  gap: 10px;
}

/* TEXTAREA */
textarea {
  height: 90px;
  resize: none;
}
.button-group{
    display:flex;
    justify-content:space-between;
    gap:12px;
    margin-top:22px;
}

.btn{
    flex:1;
    height:52px;
    border:none;
    border-radius:30px;
    background:#9a6f60;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
    text-decoration:none;
    display:flex;
    align-items:center;
    justify-content:center;
}

.btn:hover{
    background:#875f52;
}

.btn-back{
    max-width:120px;
}

.btn-submit{
    flex:1;
}
/* MESSAGE */
.msg {
  text-align: center;
  color: green;
  margin-top: 10px;
}
</style>

</head>
<body>

<h1>Pet Boarding / Grooming Appointment</h1>

<form method="POST">

<?php if (!empty($errors)): ?>
    <div class="msg" style="background:#f8d7da;color:#842029;border:1px solid #f5c2c7;margin:15px 0;border-radius:15px;padding:12px;">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- PET NAME -->
<label>Pet Name</label>
<input type="text" name="pet" value="<?= htmlspecialchars($pet) ?>" required>

<!-- TYPE -->
<label>Type</label>
<select name="type">
    <option value="Grooming" <?= $type === 'Grooming' ? 'selected' : '' ?>>Grooming</option>
    <option value="Boarding" <?= $type === 'Boarding' ? 'selected' : '' ?>>Boarding</option>
</select>

<!-- DATE -->
<label>Date In / Out</label>
<div class="date-group">
    <input type="date" name="date_in" value="<?= htmlspecialchars($date_in) ?>" required>
    <input type="date" name="date_out" value="<?= htmlspecialchars($date_out) ?>" required>
</div>

<!-- NOTE -->
<label>Note</label>
<textarea name="notes" placeholder="Add notes"><?= htmlspecialchars($notes) ?></textarea>
<div class="button-group">

    <a href="dashboard.php" class="btn btn-back">
        Back
    </a>

    <button type="submit" class="btn btn-submit">
        Submit
    </button>

</div>

</form>
<?php if ($message): ?>
<div class="msg"><?= $message ?></div>
<?php endif; ?>

</body>
</html>