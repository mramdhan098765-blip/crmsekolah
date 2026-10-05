<?php
// modules/agent/index.php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$agents = [];
$pk_column = 'id';

try {
    // Deteksi nama kolom Primary Key secara otomatis
    $stmt_cols = $pdo->query("SHOW COLUMNS FROM agent");
    $columns = $stmt_cols->fetchAll();
    
    if (count($columns) > 0) {
        $pk_column = $columns[0]['Field'];
        foreach($columns as $col) {
            if($col['Key'] == 'PRI') {
                $pk_column = $col['Field'];
                break;
            }
        }
    }

    // Ambil semua data agent
    $stmt = $pdo->query("SELECT * FROM agent ORDER BY $pk_column DESC");
    $agents = $stmt->fetchAll();
} catch (PDOException $e) {
    // Abaikan jika tabel belum ada
}

require_once '../../views/layouts/header.php';
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Kelola Data Agent / Mitra</h2>
            <a href="create.php" class="btn btn-primary">+ Tambah Agent</a>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success_add'): ?>
                <div class="alert alert-success alert-dismissible fade show">Data berhasil ditambahkan. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($_GET['status'] == 'success_edit'): ?>
                <div class="alert alert-info alert-dismissible fade show">Data berhasil diperbarui. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($_GET['status'] == 'success_delete'): ?>
                <div class="alert alert-warning alert-dismissible fade show">Data berhasil dihapus. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">No</th>
                                <th>Nama Agent</th>
                                <th>No. Telepon</th>
                                <th>Instansi / Asal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($agents)): ?>
                                <?php foreach ($agents as $index => $row): ?>
                                    <tr>
                                        <td class="px-3 align-middle"><?= $index + 1; ?></td>
                                        <td class="align-middle"><?= htmlspecialchars($row['nama'] ?? $row['nama_agent'] ?? '-'); ?></td>
                                        <td class="align-middle"><?= htmlspecialchars($row['no_telepon'] ?? $row['no_telp'] ?? '-'); ?></td>
                                        <td class="align-middle"><?= htmlspecialchars($row['instansi'] ?? $row['asal'] ?? '-'); ?></td>
                                        <td class="align-middle">
                                            <?php $status = $row['status'] ?? 'Aktif'; ?>
                                            <span class="badge bg-<?= strtolower($status) == 'aktif' ? 'success' : 'secondary'; ?>">
                                                <?= htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <!-- Tombol Edit menggunakan Bootstrap Icons -->
                                            <a href="edit.php?id=<?= $row[$pk_column]; ?>" class="btn btn-sm btn-warning text-dark" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <!-- Tombol Hapus menggunakan Bootstrap Icons -->
                                            <a href="delete.php?id=<?= $row[$pk_column]; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus agent ini?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">Belum ada data agent.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>