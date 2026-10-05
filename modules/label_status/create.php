<?php
require_once '../../config/database.php';
session_start();

// Proses Insert Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_label = $_POST['nama_label'];
    $keterangan = $_POST['keterangan'];
    $jumlah_siswa = $_POST['jumlah_siswa'];
    $status = $_POST['status'];

    // Query Insert (Silakan hilangkan komentar saat database sudah siap)
    // $insert = $pdo->prepare("INSERT INTO label_status (nama_label, keterangan, jumlah_siswa, status) VALUES (?, ?, ?, ?)");
    // $insert->execute([$nama_label, $keterangan, $jumlah_siswa, $status]);

    // Kembali ke index dengan pesan berhasil (opsional)
    header("Location: index.php?status=success_add");
    exit;
}

require_once '../../views/layouts/header.php';
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4" style="background-color: #f8f9fc;">
        <h4 class="fw-bold mb-4">Tambah Label Status</h4>
        
        <div class="card border-0 shadow-sm" style="max-width: 600px; border-radius: 8px;">
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Label</label>
                        <input type="text" name="nama_label" class="form-control" placeholder="Contoh: Closing" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Masukkan deskripsi untuk label ini..." required></textarea>
                    </div>

                    <!-- Kolom Tambahan: Jumlah Siswa -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Jumlah Siswa</label>
                        <input type="number" name="jumlah_siswa" class="form-control" value="0" min="0" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Status Label</label>
                        <select name="status" class="form-select">
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                        <a href="index.php" class="btn btn-light px-4 border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>