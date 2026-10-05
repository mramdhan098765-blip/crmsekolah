<?php
// modules/crm_siswa/edit.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM crm_siswa WHERE id_calonsiswa = ?");
$stmt->execute([$id]);
$siswa = $stmt->fetch();

if (!$siswa) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_calon   = trim($_POST['nama_calon']);
    $nama_ortu    = trim($_POST['nama_ortu']);
    $no_hp        = trim($_POST['no_hp']);
    $asal_sekolah = trim($_POST['asal_sekolah']);
    $kota         = trim($_POST['kota']);
    $sumber_info  = trim($_POST['sumber_info']);
    $jalur_daftar = trim($_POST['jalur_daftar']);
    $biaya_spp    = !empty($_POST['biaya_spp']) ? str_replace(',', '', $_POST['biaya_spp']) : 0;
    $status       = $_POST['status'];
    $tgl_followup = !empty($_POST['tgl_followup']) ? $_POST['tgl_followup'] : null;

    $sql = "UPDATE crm_siswa SET nama_calon=?, nama_ortu=?, no_hp=?, asal_sekolah=?, kota=?, sumber_info=?, jalur_daftar=?, biaya_spp=?, status=?, tgl_followup=? WHERE id_calonsiswa=?";
    $stmtUpdate = $pdo->prepare($sql);
    $stmtUpdate->execute([$nama_calon, $nama_ortu, $no_hp, $asal_sekolah, $kota, $sumber_info, $jalur_daftar, $biaya_spp, $status, $tgl_followup, $id]);
    
    echo "<script>alert('Data CRM calon siswa berhasil diupdate'); window.location.href='index.php';</script>";
    exit;
}
?>

<div class="card border-0 shadow-sm col-md-10 mx-auto">
    <div class="card-header bg-white fw-bold">Edit Prospek CRM</div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Calon Siswa *</label>
                    <input type="text" name="nama_calon" class="form-control" value="<?= htmlspecialchars($siswa['nama_calon']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_ortu" class="form-control" value="<?= htmlspecialchars($siswa['nama_ortu'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Handphone / WhatsApp *</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($siswa['no_hp'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Asal Sekolah</label>
                    <input type="text" name="asal_sekolah" class="form-control" value="<?= htmlspecialchars($siswa['asal_sekolah'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota / Domisili</label>
                    <input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($siswa['kota'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sumber Informasi</label>
                    <select name="sumber_info" class="form-select">
                        <option value="">-- Pilih --</option>
                        <option value="Brosur / Spanduk" <?= $siswa['sumber_info'] == 'Brosur / Spanduk' ? 'selected' : '' ?>>Brosur / Spanduk</option>
                        <option value="Sosial Media" <?= $siswa['sumber_info'] == 'Sosial Media' ? 'selected' : '' ?>>Sosial Media</option>
                        <option value="Kunjungan Sekolah" <?= $siswa['sumber_info'] == 'Kunjungan Sekolah' ? 'selected' : '' ?>>Kunjungan Sekolah</option>
                        <option value="Teman / Saudara" <?= $siswa['sumber_info'] == 'Teman / Saudara' ? 'selected' : '' ?>>Teman / Saudara</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jalur Pendaftaran</label>
                    <input type="text" name="jalur_daftar" class="form-control" value="<?= htmlspecialchars($siswa['jalur_daftar'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rencana Biaya SPP (Rp)</label>
                    <input type="number" name="biaya_spp" class="form-control" value="<?= floatval($siswa['biaya_spp']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status / Tahapan *</label>
                    <select name="status" class="form-select" required>
                        <option value="Prospek" <?= $siswa['status'] == 'Prospek' ? 'selected' : '' ?>>Prospek / Lead Baru</option>
                        <option value="Follow Up" <?= $siswa['status'] == 'Follow Up' ? 'selected' : '' ?>>Sedang Follow Up</option>
                        <option value="Diterima" <?= $siswa['status'] == 'Diterima' ? 'selected' : '' ?>>Diterima / Deal</option>
                        <option value="Batal" <?= $siswa['status'] == 'Batal' ? 'selected' : '' ?>>Batal</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jadwal Follow Up Selanjutnya</label>
                    <input type="date" name="tgl_followup" class="form-control" value="<?= htmlspecialchars($siswa['tgl_followup'] ?? '') ?>">
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="index.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-warning text-dark">Update Prospek</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>