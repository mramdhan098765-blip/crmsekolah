<?php
// modules/kunjungan/edit.php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
// Cari primary key dinamis
$stmt_pk = $pdo->query("SHOW COLUMNS FROM kunjungan");
$cols_info = $stmt_pk->fetchAll();
$pk_col = 'id';
foreach($cols_info as $c) {
    if($c['Key'] == 'PRI') { $pk_col = $c['Field']; break; }
}

$stmt = $pdo->prepare("SELECT * FROM kunjungan WHERE $pk_col = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kunjungan = $_POST['id_kunjungan'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $asal_sekolah = $_POST['asal_sekolah'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    
    $platform_sosmed = $_POST['platform_sosmed'] ?? '';
    $handle_sosmed = $_POST['handle_sosmed'] ?? '';
    if (!empty($platform_sosmed) && !empty($handle_sosmed)) {
        $sosmed = $platform_sosmed . ': ' . $handle_sosmed;
    } else {
        $sosmed = $handle_sosmed ?: $platform_sosmed;
    }

    $waktu = $_POST['waktu'] ?? '';
    $jenis_kunjungan = $_POST['jenis_kunjungan'] ?? '';
    $pic = $_POST['pic'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $hasil_kunjungan = $_POST['hasil_kunjungan'] ?? '';
    $status = $_POST['status'] ?? 'Follow Up';

    $foto_name = $data['foto'] ?? $data['dokumentasi'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = dirname(__DIR__, 2) . '/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name);
    }

    try {
        $db_columns = array_column($cols_info, 'Field');
        $update_data = [];

        if (in_array('id_kunjungan', $db_columns) && $pk_col != 'id_kunjungan') $update_data['id_kunjungan'] = $id_kunjungan;
        if (in_array('nama', $db_columns)) $update_data['nama'] = $nama; elseif (in_array('nama_siswa', $db_columns)) $update_data['nama_siswa'] = $nama; elseif (in_array('nama_tamu', $db_columns)) $update_data['nama_tamu'] = $nama;
        if (in_array('asal_sekolah', $db_columns)) $update_data['asal_sekolah'] = $asal_sekolah; elseif (in_array('instansi', $db_columns)) $update_data['instansi'] = $asal_sekolah;
        if (in_array('no_hp', $db_columns)) $update_data['no_hp'] = $no_hp; elseif (in_array('no_telepon', $db_columns)) $update_data['no_telepon'] = $no_hp;
        if (in_array('sosmed', $db_columns)) $update_data['sosmed'] = $sosmed; elseif (in_array('instagram', $db_columns)) $update_data['instagram'] = $sosmed;
        if (in_array('waktu', $db_columns)) $update_data['waktu'] = $waktu; elseif (in_array('tanggal', $db_columns)) $update_data['tanggal'] = $waktu;
        if (in_array('jenis_kunjungan', $db_columns)) $update_data['jenis_kunjungan'] = $jenis_kunjungan; elseif (in_array('jenis', $db_columns)) $update_data['jenis'] = $jenis_kunjungan;
        if (in_array('pic', $db_columns)) $update_data['pic'] = $pic; elseif (in_array('petugas', $db_columns)) $update_data['petugas'] = $pic;
        if (in_array('alamat', $db_columns)) $update_data['alamat'] = $alamat;
        if (in_array('hasil_kunjungan', $db_columns)) $update_data['hasil_kunjungan'] = $hasil_kunjungan; elseif (in_array('hasil', $db_columns)) $update_data['hasil'] = $hasil_kunjungan;
        if (in_array('status', $db_columns)) $update_data['status'] = $status;
        if (!empty($foto_name)) { if (in_array('foto', $db_columns)) $update_data['foto'] = $foto_name; elseif (in_array('dokumentasi', $db_columns)) $update_data['dokumentasi'] = $foto_name; }

        $set_parts = [];
        foreach (array_keys($update_data) as $col) {
            $set_parts[] = "$col = ?";
        }
        $sql_set = implode(", ", $set_parts);
        $values = array_values($update_data);
        $values[] = $id;

        $stmt_update = $pdo->prepare("UPDATE kunjungan SET $sql_set WHERE $pk_col = ?");
        $stmt_update->execute($values);
        
        header("Location: index.php?status=success_edit");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal memperbarui data: " . $e->getMessage();
    }
}

// Parsing nilai sosmed untuk form edit
$existing_sosmed = $data['sosmed'] ?? $data['instagram'] ?? '';
$parts = explode(':', $existing_sosmed, 2);
$current_platform = count($parts) > 1 ? trim($parts[0]) : 'IG';
$current_handle = count($parts) > 1 ? trim($parts[1]) : $existing_sosmed;

require_once '../../views/layouts/header.php';
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit Data Kunjungan</h4>
                <small class="text-muted">Dashboard / Kunjungan / Edit</small>
            </div>
            <a href="index.php" class="btn btn-secondary btn-sm">Batal</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">ID Kunjungan</label>
                            <input type="text" name="id_kunjungan" class="form-control" value="<?= htmlspecialchars($data['id_kunjungan'] ?? $data['id'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Siswa / Wali / Tamu</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama'] ?? $data['nama_siswa'] ?? $data['nama_tamu'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Asal Sekolah / Instansi</label>
                            <input type="text" name="asal_sekolah" class="form-control" value="<?= htmlspecialchars($data['asal_sekolah'] ?? $data['instansi'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($data['no_hp'] ?? $data['no_telepon'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Platform Sosmed</label>
                            <select name="platform_sosmed" class="form-select">
                                <option value="IG" <?= in_array($current_platform, ['IG','Instagram']) ? 'selected' : ''; ?>>Instagram (IG)</option>
                                <option value="TikTok" <?= $current_platform == 'TikTok' ? 'selected' : ''; ?>>TikTok</option>
                                <option value="FB" <?= in_array($current_platform, ['FB','Facebook']) ? 'selected' : ''; ?>>Facebook</option>
                                <option value="Twitter" <?= $current_platform == 'Twitter' ? 'selected' : ''; ?>>Twitter / X</option>
                                <option value="Lainnya" <?= !in_array($current_platform, ['IG', 'Instagram', 'TikTok', 'FB', 'Facebook', 'Twitter']) ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username / Link Sosmed</label>
                            <input type="text" name="handle_sosmed" class="form-control" value="<?= htmlspecialchars($current_handle); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Waktu Kunjungan</label>
                            <input type="datetime-local" name="waktu" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['waktu'] ?? $data['tanggal'] ?? time())); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Kunjungan</label>
                            <input type="text" name="jenis_kunjungan" class="form-control" value="<?= htmlspecialchars($data['jenis_kunjungan'] ?? $data['jenis'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">PIC (Staf / Petugas)</label>
                            <input type="text" name="pic" class="form-control" value="<?= htmlspecialchars($data['pic'] ?? $data['petugas'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Follow Up" <?= ($data['status'] ?? '') == 'Follow Up' ? 'selected' : ''; ?>>Follow Up</option>
                                <option value="Deal" <?= ($data['status'] ?? '') == 'Deal' ? 'selected' : ''; ?>>Deal</option>
                                <option value="Cancel" <?= ($data['status'] ?? '') == 'Cancel' || ($data['status'] ?? '') == 'Tolak' ? 'selected' : ''; ?>>Cancel / Tolak</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Hasil Kunjungan</label>
                            <textarea name="hasil_kunjungan" class="form-control" rows="2"><?= htmlspecialchars($data['hasil_kunjungan'] ?? $data['hasil'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Ubah Foto Dokumentasi</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Update Data Kunjungan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>