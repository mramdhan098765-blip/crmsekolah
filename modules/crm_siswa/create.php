<?php
// modules/crm_siswa/create.php
require_once '../../config/database.php';
require_once '../../views/layouts/header.php';

// Cek apakah ada parameter id_kunjungan (Auto-fill dari Data Buku Tamu)
$id_kunjungan = $_GET['id_kunjungan'] ?? null;
$prefill = [
    'nama_calon' => '',
    'no_hp' => '',
    'asal_sekolah' => ''
];

if ($id_kunjungan) {
    $stmt = $pdo->prepare("SELECT nama_siswa_wali, no_hp_wa, asal_sekolah, id_sekolah FROM kunjungan WHERE id_kunjungan = ?");
    $stmt->execute([$id_kunjungan]);
    $kunjungan = $stmt->fetch();
    if ($kunjungan) {
        $prefill['nama_calon'] = $kunjungan['nama_siswa_wali'];
        $prefill['no_hp'] = $kunjungan['no_hp_wa'];
        
        if ($kunjungan['asal_sekolah']) {
            $prefill['asal_sekolah'] = $kunjungan['asal_sekolah'];
        } elseif ($kunjungan['id_sekolah']) {
            // Ambil nama sekolah dari tabel master sekolah
            $stmtSekolah = $pdo->prepare("SELECT nama_sekolah FROM sekolah WHERE id_sekolah = ?");
            $stmtSekolah->execute([$kunjungan['id_sekolah']]);
            $prefill['asal_sekolah'] = $stmtSekolah->fetchColumn();
        }
    }
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
    $id_user      = $_SESSION['user']['id_user']; // PIC

    $sql = "INSERT INTO crm_siswa (id_kunjungan, id_user, nama_calon, nama_ortu, no_hp, asal_sekolah, kota, sumber_info, jalur_daftar, biaya_spp, status, tgl_followup) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_kunjungan, $id_user, $nama_calon, $nama_ortu, $no_hp, $asal_sekolah, $kota, $sumber_info, $jalur_daftar, $biaya_spp, $status, $tgl_followup]);
    
    echo "<script>alert('Prospek calon siswa berhasil ditambahkan'); window.location.href='index.php';</script>";
    exit;
}
?>

<div class="card border-0 shadow-sm col-md-10 mx-auto">
    <div class="card-header bg-white fw-bold">Tambah Prospek Calon Siswa (Lead)</div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Calon Siswa *</label>
                    <input type="text" name="nama_calon" class="form-control" value="<?= htmlspecialchars($prefill['nama_calon']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_ortu" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Handphone / WhatsApp *</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($prefill['no_hp']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Asal Sekolah</label>
                    <input type="text" name="asal_sekolah" class="form-control" value="<?= htmlspecialchars($prefill['asal_sekolah']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota / Domisili</label>
                    <input type="text" name="kota" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sumber Informasi</label>
                    <select name="sumber_info" class="form-select">
                        <option value="">-- Pilih --</option>
                        <option value="Brosur / Spanduk">Brosur / Spanduk</option>
                        <option value="Sosial Media">Sosial Media</option>
                        <option value="Kunjungan Sekolah">Kunjungan Sekolah</option>
                        <option value="Teman / Saudara">Teman / Saudara</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jalur Pendaftaran</label>
                    <input type="text" name="jalur_daftar" class="form-control" placeholder="Contoh: Prestasi, Reguler, dll.">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rencana Biaya SPP (Rp)</label>
                    <input type="number" name="biaya_spp" class="form-control" placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Awal *</label>
                    <select name="status" class="form-select" required>
                        <option value="Prospek">Prospek / Lead Baru</option>
                        <option value="Follow Up">Sedang Follow Up</option>
                        <option value="Diterima">Diterima / Deal</option>
                        <option value="Batal">Batal</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jadwal Follow Up Selanjutnya</label>
                    <input type="date" name="tgl_followup" class="form-control">
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="index.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Prospek</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>