<?php
// index.php - Halaman utama yang redirect ke login
require_once 'db.php';

// Jika user sudah login, redirect ke chat.php
if (isset($_SESSION['user_id'])) {
    header("Location: app.php");
    exit;
}

// Jika belum login, redirect ke login.php
header("Location: login.php");
exit;
?>