<?php
// modules/kunjungan/create.php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_siswa_wali = $_POST['nama_siswa_wali'] ?? '';
    $asal_sekolah = $_POST['asal_sekolah'] ?? '';
    $no_hp_wa = $_POST['no_hp_wa'] ?? '';
    
    // Proses Input Sosial Media
    $platform_sosmed = $_POST['platform_sosmed'] ?? '';
    $handle_sosmed = $_POST['handle_sosmed'] ?? '';
    $sosmed = '';
    if (!empty($platform_sosmed) && !empty($handle_sosmed)) {
        $sosmed = $platform_sosmed . ': ' . $handle_sosmed;
    } elseif (!empty($handle_sosmed)) {
        $sosmed = $handle_sosmed;
    }

    $alamat = $_POST['alamat'] ?? '';
    $status = $_POST['status'] ?? 'Baru';
    $jenis_kunjungan = $_POST['jenis_kunjungan'] ?? 'Offline';
    $hasil_kunjungan = $_POST['hasil_kunjungan'] ?? '';
    
    $raw_waktu = $_POST['waktu'] ?? date('Y-m-d\TH:i');
    $tgl_kunjungan = date('Y-m-d', strtotime($raw_waktu));
    $jam = date('H:i:s', strtotime($raw_waktu));

    $upload_foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = dirname(__DIR__, 2) . '/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $upload_foto = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $upload_foto);
    }

    try {
        // Query Insert Diperbarui dengan kolom sosmed
        $sql = "INSERT INTO kunjungan (
            nama_siswa_wali, asal_sekolah, no_hp_wa, sosmed, alamat, status, 
            tgl_kunjungan, jam, jenis_kunjungan, hasil_kunjungan, upload_foto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nama_siswa_wali, $asal_sekolah, $no_hp_wa, $sosmed, $alamat, $status, 
            $tgl_kunjungan, $jam, $jenis_kunjungan, $hasil_kunjungan, $upload_foto
        ]);
        
        header("Location: index.php?status=success_add");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal menyimpan data: " . $e->getMessage();
    }
}

require_once '../../views/layouts/header.php';
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Tambah Kunjungan Baru</h4>
                <small class="text-muted">Dashboard / Kunjungan / Tambah</small>
            </div>
            <a href="index.php" class="btn btn-secondary btn-sm">Kembali</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Siswa / Wali / Tamu</label>
                            <input type="text" name="nama_siswa_wali" class="form-control" placeholder="Contoh: Bpk Hermawan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Asal Sekolah</label>
                            <input type="text" name="asal_sekolah" class="form-control" placeholder="Contoh: SMPN 1 Bandung">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">No. HP / WA</label>
                            <input type="text" name="no_hp_wa" class="form-control" placeholder="Contoh: 08123456789">
                        </div>
                        
                        <!-- Input Platform & Link Sosmed Ditambahkan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Sosial Media (Opsional)</label>
                            <div class="input-group">
                                <select name="platform_sosmed" class="form-select" style="max-width: 140px;">
                                    <option value="IG">Instagram</option>
                                    <option value="TikTok">TikTok</option>
                                    <option value="FB">Facebook</option>
                                    <option value="">Lainnya/Link</option>
                                </select>
                                <input type="text" name="handle_sosmed" class="form-control" placeholder="Username (@) atau Link URL">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Waktu Kunjungan</label>
                            <input type="datetime-local" name="waktu" class="form-control" value="<?= date('Y-m-d\TH:i'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Kunjungan</label>
                            <select name="jenis_kunjungan" class="form-select">
                                <option value="Offline">Offline</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Baru">Baru</option>
                                <option value="Follow Up">Follow Up</option>
                                <option value="Deal">Deal</option>
                                <option value="Cancel">Cancel / Tolak</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Hasil Kunjungan</label>
                            <textarea name="hasil_kunjungan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Upload Foto Dokumentasi</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>