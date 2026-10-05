<?php
// modules/pengaturan/index.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

if ($_SESSION['user']['nama_role'] !== 'Administrator') {
    echo "<script>alert('Akses khusus Administrator!'); window.location.href='../../dashboard.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logika update pengaturan akan ditempatkan di sini
    echo "<script>alert('Pengaturan berhasil disimpan!'); window.location.href='index.php';</script>";
    exit;
}
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold"><i class="bi bi-gear"></i> Pengaturan Aplikasi CRM</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nama Institusi / Sekolah</label>
                        <input type="text" name="nama_institusi" class="form-control" value="Aplikasi CRM Sekolah" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Sistem (URL)</label>
                        <input type="url" name="app_url" class="form-control" value="http://localhost/crmsekolah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Kontak Admin</label>
                        <input type="email" name="email_admin" class="form-control" value="admin@sekolah.com">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Logo Sekolah</label>
                        <input type="file" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>