<?php
// auth/login.php
require_once '../config/database.php';
session_start();

if (isset($_SESSION['user'])) {
    header("Location: ../dashboard.php");
    exit;
}

$error = '';

// Ambil daftar roles untuk dropdown
$stmtRoles = $pdo->query("SELECT * FROM roles ORDER BY id_role ASC");
$roles = $stmtRoles->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_role  = $_POST['id_role'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($id_role) || empty($username) || empty($password)) {
        $error = 'Semua bidang wajib diisi!';
    } else {
        $stmt = $pdo->prepare("SELECT u.*, r.nama_role FROM users u JOIN roles r ON u.id_role = r.id_role WHERE (u.username = :uname OR u.email = :email) AND u.id_role = :role LIMIT 1");
        $stmt->execute(['uname' => $username, 'email' => $username, 'role' => $id_role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'Aktif') {
                $error = 'Akun Anda telah dinonaktifkan.';
            } else {
                $_SESSION['user'] = [
                    'id_user' => $user['id_user'],
                    'nama' => $user['nama'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'id_role' => $user['id_role'],
                    'nama_role' => $user['nama_role']
                ];
                header("Location: ../dashboard.php");
                exit;
            }
        } else {
            $error = 'Username, Password, atau Peran tidak cocok!';
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
        .btn-primary-custom {
            background-color: #4a6572;
            border: none;
            font-size: 14px;
            font-weight: 600;
            padding: 10px;
            border-radius: 6px;
            width: 100%;
        }
        .btn-primary-custom:hover {
            background-color: #344955;
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
        <button type="button" class="btn active">Masuk</button>
        <a href="register.php" class="btn btn-inactive">Daftar Akun</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 text-start small mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Form Login -->
    <form action="" method="POST" class="text-start">
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
            <label class="form-label">Username / Email</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
        </div>

        <div class="mb-4">
            <label class="form-label">Kata Sandi (Password)</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-primary-custom text-white">Masuk ke Sistem</button>
    </form>

    <div class="footer-text">
         2026 Sistem CRM & Kunjungan Sekolah
    </div>
</div>

</body>
</html>