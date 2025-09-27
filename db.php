<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Jika pakai HTTPS
session_start();

$host = '';
$dbname = '';
$username = '';
$password = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Set timezone sesuai hosting
    $pdo->exec("SET time_zone = '+07:00'"); // Sesuaikan dengan zona waktu Anda
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>