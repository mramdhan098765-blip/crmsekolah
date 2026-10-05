<?php
// modules/crm_siswa/index.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

// Fitur Search sederhana
$search = trim($_GET['search'] ?? '');
$params = [];
$whereClause = "";

if ($search !== '') {
    $whereClause = " WHERE c.nama_calon LIKE :search OR c.asal_sekolah LIKE :search OR c.status LIKE :search ";
    $params['search'] = "%$search%";
}

// Ambil data CRM calon siswa dan gabungkan dengan tabel kunjungan & PIC
$sql = "
    SELECT c.*, k.tgl_kunjungan, u.nama as nama_pic 
    FROM crm_siswa c
    LEFT JOIN kunjungan k ON c.id_kunjungan = k.id_kunjungan
    LEFT JOIN users u ON c.id_user = u.id_user
    $whereClause
    ORDER BY c.id_calonsiswa DESC
";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
$stmt->execute();
$dataSiswa = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Pipeline CRM Calon Siswa</h5>
    <a href="create.php" class="btn btn-sm btn-primary"><i class="bi bi-person-add"></i> Tambah Prospek (Lead)</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama calon siswa / sekolah..." value="<?= htmlspecialchars($search) ?>">
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
                        <th>Nama Calon Siswa</th>
                        <th>No. HP</th>
                        <th>Asal Sekolah</th>
                        <th>Sumber Info</th>
                        <th>PIC</th>
                        <th>Follow Up</th>
                        <th>Status / Label</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($dataSiswa) > 0): ?>
                        <?php foreach ($dataSiswa as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_calon']) ?></td>
                                <td><i class="bi bi-whatsapp text-success"></i> <?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['asal_sekolah'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['sumber_info'] ?? '-') ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($row['nama_pic'] ?? '-') ?></small></td>
                                <td><?= $row['tgl_followup'] ? htmlspecialchars($row['tgl_followup']) : '<span class="badge bg-light text-dark">Belum Diatur</span>' ?></td>
                                <td>
                                    <?php 
                                        $badgeColor = 'bg-primary';
                                        if($row['status'] == 'Diterima') $badgeColor = 'bg-success';
                                        if($row['status'] == 'Batal') $badgeColor = 'bg-danger';
                                        if($row['status'] == 'Follow Up') $badgeColor = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badgeColor ?>"><?= htmlspecialchars($row['status']) ?></span>
                                </td>
                                <td class="text-center">
                                    <!-- Logika tracking Tahapan CRM / Funneling ada disini -->
                                    <a href="edit.php?id=<?= $row['id_calonsiswa'] ?>" class="btn btn-sm btn-warning mb-1"><i class="bi bi-pencil-square"></i></a>
                                    <a href="delete.php?id=<?= $row['id_calonsiswa'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Hapus data prospek ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted">Belum ada data calon siswa.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>