<?php
// auth/register.php
require_once '../config/database.php';
session_start();

if (isset($_SESSION['user'])) {
    header("Location: ../dashboard.php");
    exit;
}

$error = '';
$success = '';

// Ambil daftar roles untuk dropdown (kecuali admin jika diinginkan)
$stmtRoles = $pdo->query("SELECT * FROM roles ORDER BY id_role ASC");
$roles = $stmtRoles->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $id_role  = $_POST['id_role'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email    = strtolower($username) . '@sekolah.sch.id'; // default fallback email

    if (empty($nama) || empty($id_role) || empty($username) || empty($password)) {
        $error = 'Semua kolom wajib diisi!';
    } else {
        // Cek apakah username sudah dipakai
        $stmtCheck = $pdo->prepare("SELECT id_user FROM users WHERE username = :uname LIMIT 1");
        $stmtCheck->execute(['uname' => $username]);
        if ($stmtCheck->rowCount() > 0) {
            $error = 'Username sudah digunakan, silakan pilih yang lain.';
        } else {
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $stmtIns = $pdo->prepare("INSERT INTO users (id_role, nama, username, email, password, status) VALUES (:role, :nama, :uname, :email, :pass, 'Aktif')");
            $res = $stmtIns->execute([
                'role' => $id_role,
                'nama' => $nama,
                'uname' => $username,
                'email' => $email,
                'pass' => $hashedPass
            ]);

            if ($res) {
                $success = 'Pendaftaran berhasil! Silakan masuk.';
            } else {
                $error = 'Gagal melakukan pendaftaran.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Pendaftaran - Sistem CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            padding: 35px 30px;
        }
        .brand-icon {
            width: 48px;
            height: 48px;
            background: #f0f4ff;
            border: 1px solid #d0e0ff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            color: #3b82f6;
            font-size: 24px;
        }
        .btn-toggle-group {
            background: #f1f5f9;
            padding: 4px;
            border-radius: 25px;
            display: flex;
            margin-bottom: 25px;
        }
        .btn-toggle-group .btn {
            border-radius: 20px;
            flex: 1;
            font-weight: 500;
            font-size: 14px;
            padding: 6px 12px;
        }
        .btn-toggle-group .btn.active {
            background-color: #4a6572;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-toggle-group .btn-inactive {
            color: #64748b;
            text-decoration: none;
        }
        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .form-control, .form-select {
            font-size: 13px;
            padding: 9px 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }
        .form-control::placeholder {
            color: #94a3b8;
        }
        .btn-register-custom {
            background-color: #198754;
            border: none;
            font-size: 14px;
            font-weight: 600;
            padding: 10px;
            border-radius: 6px;
            width: 100%;
        }
        .btn-register-custom:hover {
            background-color: #157347;
        }
        .footer-text {
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            margin-top: 25px;
        }
    </style>
</head>
<body>

<div class="auth-card text-center">
    <!-- Brand Icon -->
    <div class="brand-icon">
        <i class="bi bi-box-arrow-in-right"></i>
    </div>

    <h5 class="fw-bold mb-1" style="color: #1e293b;">CRM & Buku Tamu</h5>
    <p class="text-muted small mb-4" style="font-size: 12px;">Sistem Informasi Terpadu Sekolah</p>

    <!-- Tab Buttons -->
    <div class="btn-toggle-group">
        <a href="login.php" class="btn btn-inactive">Masuk</a>
        <button type="button" class="btn active">Daftar Akun</button>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 text-start small mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success py-2 text-start small mb-3"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Form Register -->
    <form action="" method="POST" class="text-start">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pilih Peran Pengguna (Role)</label>
            <select name="id_role" class="form-select" required>
                <option value="">-- Pilih Hak Akses --</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id_role'] ?>"><?= htmlspecialchars($r['nama_role']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Username Akun</label>
            <input type="text" name="username" class="form-control" placeholder="Buat username" required autocomplete="off">
        </div>

        <div class="mb-4">
            <label class="form-label">Kata Sandi (Password)</label>
            <input type="password" name="password" class="form-control" placeholder="Buat password" required>
        </div>

        <button type="submit" class="btn btn-success btn-register-custom text-white">Daftar Sekarang</button>
    </form>

    <div class="footer-text">
         2026 Sistem CRM & Kunjungan Sekolah
    </div>
</div>

</body>
</html>