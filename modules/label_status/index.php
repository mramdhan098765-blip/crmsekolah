<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// 1. Cek apakah tabel kosong. Jika kosong, otomatis insert 3 data default (Closing, Minat, Ragu) dengan jumlah = 0
try {
    $stmt_check = $pdo->query("SELECT COUNT(*) FROM label_status");
    $count_data = $stmt_check->fetchColumn();

    if ($count_data == 0) {
        $default_labels = [
            ['nama_label' => 'Closing', 'keterangan' => 'Calon siswa sudah yakin dan melakukan pendaftaran.', 'jumlah_siswa' => 0, 'status' => 'Aktif'],
            ['nama_label' => 'Minat', 'keterangan' => 'Calon siswa tertarik dan masih dalam proses follow up.', 'jumlah_siswa' => 0, 'status' => 'Aktif'],
            ['nama_label' => 'Ragu', 'keterangan' => 'Calon siswa masih mempertimbangkan atau belum yakin.', 'jumlah_siswa' => 0, 'status' => 'Aktif']
        ];

        $stmt_insert = $pdo->prepare("INSERT INTO label_status (nama_label, keterangan, jumlah_siswa, status) VALUES (?, ?, ?, ?)");
        
        foreach ($default_labels as $lbl) {
            $stmt_insert->execute([$lbl['nama_label'], $lbl['keterangan'], $lbl['jumlah_siswa'], $lbl['status']]);
        }
    }
} catch (PDOException $e) {
    // Abaikan jika tabel belum ada, pastikan tabel label_status sudah dibuat di database
}

// 2. Ambil semua data label dari database (setelah pengecekan default di atas)
$labels = [];
try {
    $stmt = $pdo->query("SELECT * FROM label_status ORDER BY id ASC");
    $labels = $stmt->fetchAll();
} catch (PDOException $e) {
    // Abaikan
}

// Inisialisasi variabel untuk 3 Card Ringkasan di atas
$count_closing = 0;
$count_minat = 0;
$count_ragu = 0;

// Update nilai berdasarkan data dari database
foreach ($labels as $row) {
    $nama = strtolower($row['nama_label']);
    if (strpos($nama, 'closing') !== false) {
        $count_closing = $row['jumlah_siswa'];
    } elseif (strpos($nama, 'minat') !== false) {
        $count_minat = $row['jumlah_siswa'];
    } elseif (strpos($nama, 'ragu') !== false) {
        $count_ragu = $row['jumlah_siswa'];
    }
}

require_once '../../views/layouts/header.php';
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4" style="background-color: #f8f9fc;">
        
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Label Status CRM</h4>
            <small class="text-muted">Dashboard / CRM / Label Status</small>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success_edit'): ?>
                <div class="alert alert-success alert-dismissible fade show" style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
                    Data label berhasil diperbarui. 
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'success_delete'): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    Data label berhasil dihapus. 
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'success_add'): ?>
                <div class="alert alert-primary alert-dismissible fade show" style="background-color: #e3f2fd; border: 1px solid #b6d4fe; color: #084298;">
                    Label baru berhasil ditambahkan. 
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold mb-1">Kelola Kategori Label Status</h6>
                <small class="text-muted">Kelola kategori tingkat ketertarikan calon siswa terhadap sekolah.</small>
            </div>
            <a href="create.php" class="btn btn-primary btn-sm px-4 py-2 fw-medium" style="background-color: #2c3e50; border-color: #2c3e50;">
                + Tambah Label
            </a>
        </div>

        <!-- 3 Cards Ringkasan -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 8px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success rounded me-2" style="width: 14px; height: 14px;"></div>
                            <h6 class="fw-bold mb-0">Closing <small class="text-muted fw-normal ms-1" style="font-size: 0.75rem;">LBL001</small></h6>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.85rem;">Calon siswa sudah yakin dan melakukan pendaftaran.</p>
                        <hr class="text-muted opacity-25">
                        <small class="text-muted d-block mb-1">Jumlah Siswa</small>
                        <h5 class="text-success fw-bold mb-0"><?= $count_closing; ?> <small class="fw-normal" style="font-size: 0.9rem;">siswa</small></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 8px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-warning rounded me-2" style="width: 14px; height: 14px;"></div>
                            <h6 class="fw-bold mb-0">Minat <small class="text-muted fw-normal ms-1" style="font-size: 0.75rem;">LBL002</small></h6>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.85rem;">Calon siswa tertarik dan masih dalam proses follow up.</p>
                        <hr class="text-muted opacity-25">
                        <small class="text-muted d-block mb-1">Jumlah Siswa</small>
                        <h5 class="text-warning fw-bold mb-0"><?= $count_minat; ?> <small class="fw-normal" style="font-size: 0.9rem;">siswa</small></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 8px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-danger rounded me-2" style="width: 14px; height: 14px;"></div>
                            <h6 class="fw-bold mb-0">Ragu <small class="text-muted fw-normal ms-1" style="font-size: 0.75rem;">LBL003</small></h6>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.85rem;">Calon siswa masih mempertimbangkan atau belum yakin.</p>
                        <hr class="text-muted opacity-25">
                        <small class="text-muted d-block mb-1">Jumlah Siswa</small>
                        <h5 class="text-danger fw-bold mb-0"><?= $count_ragu; ?> <small class="fw-normal" style="font-size: 0.9rem;">siswa</small></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Label -->
        <div class="card border-0 shadow-sm" style="border-radius: 8px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover bg-white">
                        <thead>
                            <tr style="border-bottom: 2px solid #f0f0f0;">
                                <th class="px-4 py-3 text-dark fw-bold" style="width: 5%; font-size: 0.9rem;">No</th>
                                <th class="py-3 text-dark fw-bold" style="width: 10%; font-size: 0.9rem;">ID Label</th>
                                <th class="py-3 text-dark fw-bold" style="width: 15%; font-size: 0.9rem;">Nama Label</th>
                                <th class="py-3 text-dark fw-bold" style="width: 35%; font-size: 0.9rem;">Keterangan</th>
                                <th class="py-3 text-dark fw-bold text-center" style="width: 15%; font-size: 0.9rem;">Jumlah Siswa</th>
                                <th class="py-3 text-dark fw-bold" style="width: 10%; font-size: 0.9rem;">Status</th>
                                <th class="py-3 text-dark fw-bold text-center" style="width: 10%; font-size: 0.9rem;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($labels)): ?>
                                <?php $no = 1; foreach ($labels as $row): 
                                    // Generate format ID LBL001, LBL002
                                    $id_label = sprintf("LBL%03d", $row['id']);
                                    
                                    // Atur warna titik berdasarkan nama label
                                    $dot_color = '#0d6efd'; // default blue
                                    $nama_cek = strtolower($row['nama_label']);
                                    if (strpos($nama_cek, 'closing') !== false) $dot_color = '#198754'; // green
                                    elseif (strpos($nama_cek, 'minat') !== false) $dot_color = '#ffc107'; // yellow
                                    elseif (strpos($nama_cek, 'ragu') !== false) $dot_color = '#dc3545'; // red
                                ?>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="px-4 py-3 text-dark" style="font-size: 0.9rem;"><?= $no++; ?></td>
                                    <td class="py-3 text-muted" style="font-size: 0.9rem;"><?= $id_label; ?></td>
                                    <td class="py-3 fw-medium" style="font-size: 0.9rem; color: #444;">
                                        <i class="fas fa-circle me-2" style="color: <?= $dot_color; ?>; font-size: 0.5rem; vertical-align: middle;"></i>
                                        <?= htmlspecialchars($row['nama_label']); ?>
                                    </td>
                                    <td class="py-3 text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($row['keterangan']); ?></td>
                                    <td class="py-3 text-center">
                                        <!-- Kotak biru jumlah siswa sesuai gambar kedua -->
                                        <span class="badge bg-primary px-3 py-2 fw-normal" style="font-size: 0.85rem; border-radius: 4px;">
                                            <?= htmlspecialchars($row['jumlah_siswa']); ?> Siswa
                                        </span>
                                    </td>
                                    <td class="py-3 fw-medium" style="font-size: 0.85rem; color: #198754;">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-primary px-3 py-1" style="font-size: 0.8rem; border-radius: 4px;">Edit</a>
                                            <a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus label ini?');" class="btn btn-danger px-3 py-1" style="font-size: 0.8rem; border-radius: 4px;">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data label.</td>
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