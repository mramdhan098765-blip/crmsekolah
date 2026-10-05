<?php
// views/layouts/header.php
require_once __DIR__ . '/../../config/auth.php';
checkAuth();
$currentUser = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Sekolah - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-size: 14px; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #fff; }
        .sidebar a { color: #cbd5e1; text-decoration: none; padding: 10px 15px; display: block; border-radius: 6px; margin-bottom: 4px; }
        .sidebar a:hover, .sidebar a.active { background-color: #334155; color: #fff; }
        .main-content { padding: 25px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Include Sidebar -->
        <?php include_once __DIR__ . '/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
                <h1 class="h4">Sistem CRM & Buku Tamu Sekolah</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary p-2 me-2">Role: <?= htmlspecialchars($currentUser['nama_role']) ?></span>
                    <a href="/crmsekolah/auth/logout.php" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
                </div>
            </div>