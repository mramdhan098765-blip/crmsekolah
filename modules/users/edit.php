<?php
// modules/users/edit.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

if ($_SESSION['user']['nama_role'] !== 'Administrator') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='../../dashboard.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID User tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

// Ambil data user
$stmtUser = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmtUser->execute([$id]);
$user = $stmtUser->fetch();

if (!$user) {
    echo "<script>alert('Data user tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

// Ambil data roles
$stmtRoles = $pdo->query("SELECT * FROM roles ORDER BY id_role ASC");
$roles = $stmtRoles->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password']; // Jika kosong, berarti tidak diubah
    $id_role  = $_POST['id_role'];
    $status   = $_POST['status'];
    
    // Cek duplikasi username/email selain milik sendiri
    $cek = $pdo->prepare("SELECT id_user FROM users WHERE (username = ? OR email = ?) AND id_user != ?");
    $cek->execute([$username, $email, $id]);
    if ($cek->rowCount() > 0) {
        echo "<script>alert('Username atau Email sudah terdaftar pada akun lain!');</script>";
    } else {
        if (!empty($password)) {
            // Update dengan password baru
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET id_role=?, nama=?, username=?, email=?, password=?, status=? WHERE id_user=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_role, $nama, $username, $email, $hashedPass, $status, $id]);
        } else {
            // Update tanpa mengubah password
            $sql = "UPDATE users SET id_role=?, nama=?, username=?, email=?, status=? WHERE id_user=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_role, $nama, $username, $email, $status, $id]);
        }
        echo "<script>alert('Data user berhasil diupdate'); window.location.href='index.php';</script>";
        exit;
    }
}
?>

<div class="card border-0 shadow-sm col-md-6 mx-auto">
    <div class="card-header bg-white fw-bold">Edit Pengguna</div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password baru...">
            </div>
            <div class="mb-3">
                <label class="form-label">Role Akses *</label>
                <select name="id_role" class="form-select" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id_role'] ?>" <?= $user['id_role'] == $r['id_role'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['nama_role']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="Aktif" <?= $user['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Nonaktif" <?= $user['status'] === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="text-end">
                <a href="index.php" class="btn btn-secondary btn-sm">Batal</a>
                <button type="submit" class="btn btn-warning btn-sm text-dark">Update Data</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>