<?php
// includes/init.php
session_start();

if (getenv('DB_HOST')) {
    define('BASE_URL', ''); // Chạy trong Docker
} else {
    define('BASE_URL', '/QuanLyCuaHang'); // Chạy trong XAMPP
}
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/db.php';

// Check login status (except for login page)
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php' && $current_page !== 'setup.php') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/pages/auth/login.php");
        exit;
    }
}
?>
