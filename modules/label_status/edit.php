<?php
require_once '../../config/database.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Ambil data spesifik dari database untuk diisikan ke form
$stmt = $pdo->prepare("SELECT * FROM label_status WHERE id = ?");
$stmt->execute([$id]);
$label = $stmt->fetch();

if (!$label) {
    header("Location: index.php");
    exit;
}

// Proses Update Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_label = $_POST['nama_label'];
    $keterangan = $_POST['keterangan'];
    $jumlah_siswa = $_POST['jumlah_siswa']; // Menangkap angka jumlah siswa baru
    $status = $_POST['status'];

    // Update data di database
    $update = $pdo->prepare("UPDATE label_status SET nama_label = ?, keterangan = ?, jumlah_siswa = ?, status = ? WHERE id = ?");
    $update->execute([$nama_label, $keterangan, $jumlah_siswa, $status, $id]);

    header("Location: index.php?status=success_edit");
    exit;
}

require_once '../../views/layouts/header.php';
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4" style="background-color: #f8f9fc;">
        <h4 class="fw-bold mb-4">Edit Label Status</h4>
        
        <div class="card border-0 shadow-sm" style="max-width: 600px; border-radius: 8px;">
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Label</label>
                        <!-- Menampilkan data dari database -->
                        <input type="text" name="nama_label" class="form-control" value="<?= htmlspecialchars($label['nama_label']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required><?= htmlspecialchars($label['keterangan']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Jumlah Siswa</label>
                        <!-- Menampilkan angka jumlah siswa dari database -->
                        <input type="number" name="jumlah_siswa" class="form-control" value="<?= htmlspecialchars($label['jumlah_siswa']); ?>" min="0" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Status Label</label>
                        <select name="status" class="form-select">
                            <option value="Aktif" <?= $label['status'] == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="Tidak Aktif" <?= $label['status'] == 'Tidak Aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        <a href="index.php" class="btn btn-light px-4 border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>