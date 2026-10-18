<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

if (!function_exists('hash_equals')) {
    function hash_equals($known_string, $user_string)
    {
        if (strlen($known_string) !== strlen($user_string)) {
            return false;
        }

        $res = 0;
        $len = strlen($known_string);
        for ($i = 0; $i < $len; $i++) {
            $res |= ord($known_string[$i]) ^ ord($user_string[$i]);
        }

        return $res === 0;
    }
}

if (!function_exists('password_hash')) {
    function password_hash($password, $algo, array $options = array())
    {
        if ($algo !== PASSWORD_DEFAULT && $algo !== PASSWORD_BCRYPT) {
            trigger_error('password_hash(): Unknown password hashing algorithm', E_USER_WARNING);
            return false;
        }

        $cost = isset($options['cost']) ? $options['cost'] : 10;
        if ($cost < 4 || $cost > 31) {
            trigger_error('password_hash(): Invalid bcrypt cost parameter specified: ' . $cost, E_USER_WARNING);
            return false;
        }

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./';
        $salt = '';
        for ($i = 0; $i < 22; $i++) {
            $salt .= $chars[mt_rand(0, 63)];
        }

        $salt = sprintf('$2y$%02d$%s', $cost, $salt);
        $hash = crypt($password, $salt);

        if (!is_string($hash) || strlen($hash) !== 60) {
            return false;
        }

        return $hash;
    }
}

if (!function_exists('password_verify')) {
    function password_verify($password, $hash)
    {
        $ret = crypt($password, $hash);
        if (!is_string($ret) || strlen($ret) !== strlen($hash)) {
            return false;
        }

        return hash_equals($ret, $hash);
    }
}

// Localhost XAMPP configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pawfect';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

if (! $conn->set_charset('utf8mb4')) {
    die('Gagal mengatur charset database: ' . $conn->error);
}

