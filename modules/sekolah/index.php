<?php
// modules/sekolah/index.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

// Fitur Search & Pagination
$search = trim($_GET['search'] ?? '');
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 5;
$offset = ($page - 1) * $limit;

$params = [];
$whereClause = "";

if ($search !== '') {
    $whereClause = " WHERE nama_sekolah LIKE :search OR kota LIKE :search OR nis LIKE :search ";
    $params['search'] = "%$search%";
}

// Total rows
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM sekolah $whereClause");
$stmtTotal->execute($params);
$totalRows = $stmtTotal->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch data
$sql = "SELECT * FROM sekolah $whereClause ORDER BY id_sekolah DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
$stmt->execute();
$dataSekolah = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Master Data Sekolah</h5>
    <a href="create.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Tambah Sekolah</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Form Search -->
        <form method="GET" action="" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama sekolah / kota / NIS..." value="<?= htmlspecialchars($search) ?>">
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
                        <th>NIS</th>
                        <th>Nama Sekolah</th>
                        <th>Kepala Sekolah</th>
                        <th>Kota</th>
                        <th>No. Telp</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($dataSekolah) > 0): ?>
                        <?php foreach ($dataSekolah as $index => $row): ?>
                            <tr>
                                <td><?= $offset + $index + 1 ?></td>
                                <td><?= htmlspecialchars($row['nis'] ?? '-') ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_sekolah']) ?></td>
                                <td><?= htmlspecialchars($row['kepala_sekolah'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['kota'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['no_telp'] ?? '-') ?></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $row['id_sekolah'] ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-square"></i></a>
                                    <a href="delete.php?id=<?= $row['id_sekolah'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Data sekolah tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>