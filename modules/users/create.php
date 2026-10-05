<?php
// modules/users/create.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

$stmtRoles = $pdo->query("SELECT * FROM roles ORDER BY id_role ASC");
$roles = $stmtRoles->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $id_role  = $_POST['id_role'];
    $status   = $_POST['status'];
    
    // Cek duplikasi
    $cek = $pdo->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
    $cek->execute([$username, $email]);
    if ($cek->rowCount() > 0) {
        echo "<script>alert('Username atau Email sudah terdaftar!');</script>";
    } else {
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (id_role, nama, username, email, password, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_role, $nama, $username, $email, $hashedPass, $status]);
        echo "<script>alert('User berhasil ditambahkan'); window.location.href='index.php';</script>";
        exit;
    }
}
?>

<div class="card border-0 shadow-sm col-md-6 mx-auto">
    <div class="card-header bg-white fw-bold">Tambah Pengguna</div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role Akses *</label>
                <select name="id_role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id_role'] ?>"><?= $r['nama_role'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="text-end">
                <a href="index.php" class="btn btn-secondary btn-sm">Batal</a>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>