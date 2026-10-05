<?php
// index.php
session_start();

// Jika pengguna sudah login, arahkan ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
} else {
    // Jika belum login, arahkan ke halaman login
    header("Location: auth/login.php");
    exit;
}