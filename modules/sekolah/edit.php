<?php
// modules/sekolah/edit.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

// Fetch Existing Data
$stmt = $pdo->prepare("SELECT * FROM sekolah WHERE id_sekolah = ?");
$stmt->execute([$id]);
$sekolah = $stmt->fetch();

if (!$sekolah) {
    echo "<script>alert('Data sekolah tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis            = trim($_POST['nis']);
    $nama_sekolah   = trim($_POST['nama_sekolah']);
    $kepala_sekolah = trim($_POST['kepala_sekolah']);
    $email          = trim($_POST['email']);
    $no_telp        = trim($_POST['no_telp']);
    $kota           = trim($_POST['kota']);
    $alamat         = trim($_POST['alamat']);

    if (empty($nama_sekolah)) {
        $error = 'Nama Sekolah wajib diisi!';
    } else {
        $sql = "UPDATE sekolah SET nis=?, nama_sekolah=?, kepala_sekolah=?, email=?, no_telp=?, kota=?, alamat=? WHERE id_sekolah=?";
        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->execute([$nis, $nama_sekolah, $kepala_sekolah, $email, $no_telp, $kota, $alamat, $id]);
        
        echo "<script>alert('Data sekolah berhasil diupdate'); window.location.href='index.php';</script>";
        exit;
    }
}
?>

<div class="card border-0 shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-white fw-bold">Edit Data Sekolah</div>
    <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger py-2 small"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">NIS</label>
                    <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($sekolah['nis'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Sekolah *</label>
                    <input type="text" name="nama_sekolah" class="form-control" required value="<?= htmlspecialchars($sekolah['nama_sekolah']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kepala Sekolah</label>
                    <input type="text" name="kepala_sekolah" class="form-control" value="<?= htmlspecialchars($sekolah['kepala_sekolah'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($sekolah['no_telp'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($sekolah['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($sekolah['kota'] ?? '') ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($sekolah['alamat'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="index.php" class="btn btn-secondary btn-sm">Batal</a>
                <button type="submit" class="btn btn-warning btn-sm text-dark">Update Data</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>