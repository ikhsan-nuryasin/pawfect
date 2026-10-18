<?php
// admin_logout.php - Logout admin
session_start();
session_destroy();
header('Location: admin_login.php');
exit();
?>
