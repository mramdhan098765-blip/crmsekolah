<?php
// config/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAuth() {
    if (!isset($_SESSION['user'])) {
        header("Location: /crmsekolah/auth/login.php");
        exit;
    }
}

function hasRole($roles = []) {
    if (!isset($_SESSION['user'])) return false;
    return in_array($_SESSION['user']['nama_role'], $roles);
}

function checkRoleAccess($roles = []) {
    checkAuth();
    if (!hasRole($roles)) {
        echo "<script>alert('Akses Ditolak! Anda tidak memiliki izin.'); window.location.href='/crmsekolah/dashboard.php';</script>";
        exit;
    }
}