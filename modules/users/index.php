<?php
// modules/users/index.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

// Proteksi: Hanya Admin
if ($_SESSION['user']['nama_role'] !== 'Administrator') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='../../dashboard.php';</script>";
    exit;
}

$search = trim($_GET['search'] ?? '');
$params = [];
$whereClause = "";

if ($search !== '') {
    $whereClause = " WHERE u.nama LIKE :search OR u.username LIKE :search OR r.nama_role LIKE :search ";
    $params['search'] = "%$search%";
}

$sql = "SELECT u.*, r.nama_role FROM users u LEFT JOIN roles r ON u.id_role = r.id_role $whereClause ORDER BY u.id_user DESC";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
$stmt->execute();
$dataUsers = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Kelola Pengguna Sistem</h5>
    <a href="create.php" class="btn btn-sm btn-primary"><i class="bi bi-person-plus"></i> Tambah User</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / username / role..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-search"></i> Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Peran (Role)</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($dataUsers) > 0): ?>
                        <?php foreach ($dataUsers as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nama_role'] ?? '-') ?></span></td>
                                <td>
                                    <?php if($row['status'] === 'Aktif'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $row['id_user'] ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-square"></i></a>
                                    <?php if($row['id_user'] != $_SESSION['user']['id_user']): // Cegah hapus diri sendiri ?>
                                        <a href="delete.php?id=<?= $row['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Data pengguna tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>