<?php
// modules/kunjungan/index.php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// Function Helper untuk mengubah teks Sosmed menjadi Link Aktif
function renderSosmedLink($sosmed_text) {
    if (empty($sosmed_text) || $sosmed_text == '-') return '-';

    $sosmed = trim($sosmed_text);
    $platform = '';
    $handle = $sosmed;

    if (strpos($sosmed, ':') !== false) {
        $parts = explode(':', $sosmed, 2);
        $platform = trim($parts[0]);
        $handle = trim($parts[1]);
    }

    $clean_handle = ltrim($handle, '@');
    $url = '#';

    if (filter_var($handle, FILTER_VALIDATE_URL)) {
        $url = $handle;
    } elseif (strripos($platform, 'IG') !== false || strripos($platform, 'Instagram') !== false) {
        $url = "https://instagram.com/" . $clean_handle;
    } elseif (strripos($platform, 'TikTok') !== false) {
        $url = "https://tiktok.com/@" . $clean_handle;
    } elseif (strripos($platform, 'FB') !== false || strripos($platform, 'Facebook') !== false) {
        $url = "https://facebook.com/" . $clean_handle;
    } elseif (strripos($platform, 'Twitter') !== false || strripos($platform, 'X') !== false) {
        $url = "https://x.com/" . $clean_handle;
    } else {
        $url = "https://instagram.com/" . $clean_handle;
    }

    $displayText = !empty($platform) ? "{$platform}: {$handle}" : $handle;

    return '<a href="' . htmlspecialchars($url) . '" target="_blank" class="text-primary text-decoration-none fw-medium"><i class="fas fa-link me-1"></i>' . htmlspecialchars($displayText) . '</a>';
}

$kunjungan = [];
try {
    $stmt = $pdo->query("SELECT * FROM kunjungan ORDER BY id_kunjungan DESC");
    $kunjungan = $stmt->fetchAll();
} catch (PDOException $e) {
    // Error handling
}

require_once '../../views/layouts/header.php';
$modals_html = ''; 
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Daftar Tamu & Kunjungan</h4>
                <small class="text-muted">Dashboard / Kunjungan</small>
            </div>
            <a href="create.php" class="btn btn-primary btn-sm px-3">+ Tambah Kunjungan</a>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success_add'): ?>
                <div class="alert alert-success alert-dismissible fade show">Data kunjungan berhasil ditambahkan. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($_GET['status'] == 'success_delete'): ?>
                <div class="alert alert-warning alert-dismissible fade show">Data kunjungan berhasil dihapus. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-secondary">Data Riwayat & Kunjungan Tamu</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7">
                            <tr>
                                <th class="px-3" style="width: 90px;">ID</th>
                                <th>NAMA & ASAL SEKOLAH</th>
                                <th style="width: 140px;">WAKTU</th>
                                <th>JENIS & PIC</th>
                                <th>NO. HP / WA & SOSMED</th>
                                <th style="width: 100px;">STATUS</th>
                                <th class="text-center" style="width: 70px;">FOTO</th>
                                <th class="text-center" style="width: 160px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($kunjungan)): ?>
                                <?php foreach ($kunjungan as $row): 
                                    $row_id = (int)$row['id_kunjungan']; // Pastikan ini integer
                                    
                                    // FORMAT ID KJN001 (Tidak mempengaruhi database asli, hanya tampilan)
                                    $formatted_id = sprintf("KJN%03d", $row_id);

                                    $nama = !empty($row['nama_siswa_wali']) ? $row['nama_siswa_wali'] : '-';
                                    $asal = !empty($row['asal_sekolah']) ? $row['asal_sekolah'] : '-';
                                    $waktu = $row['tgl_kunjungan'] . ' ' . $row['jam'];
                                    $jenis = !empty($row['jenis_kunjungan']) ? $row['jenis_kunjungan'] : '-';
                                    $hp = !empty($row['no_hp_wa']) ? $row['no_hp_wa'] : '-';
                                    $sosmed = !empty($row['sosmed']) ? $row['sosmed'] : '-';
                                    $status = !empty($row['status']) ? $row['status'] : 'Baru';
                                    $alamat = !empty($row['alamat']) ? $row['alamat'] : '-';
                                    $hasil = !empty($row['hasil_kunjungan']) ? $row['hasil_kunjungan'] : '-';
                                    $foto = !empty($row['upload_foto']) ? $row['upload_foto'] : '';

                                    // Pengaturan Warna Badge Status
                                    $badge_color = 'warning text-dark';
                                    if (strtolower($status) == 'deal') $badge_color = 'success';
                                    elseif (strtolower($status) == 'cancel' || strtolower($status) == 'tolak') $badge_color = 'danger';
                                    elseif (strtolower($status) == 'baru') $badge_color = 'info text-dark';
                                ?>
                                    <tr>
                                        <td class="px-3 fw-bold text-secondary"><?= $formatted_id; ?></td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($nama); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($asal); ?></small>
                                        </td>
                                        <td>
                                            <div><?= date('d M Y', strtotime($row['tgl_kunjungan'])); ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($row['jam'])); ?></small>
                                        </td>
                                        <td>
                                            <div><?= htmlspecialchars($jenis); ?></div>
                                            <small class="text-muted">Admin Sekolah</small>
                                        </td>
                                        <td>
                                            <div><?= htmlspecialchars($hp); ?></div>
                                            <small><?= renderSosmedLink($sosmed); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $badge_color; ?> px-2 py-1"><?= htmlspecialchars($status); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($foto) && file_exists('../../uploads/' . $foto)): ?>
                                                <img src="../../uploads/<?= htmlspecialchars($foto); ?>" alt="Foto" class="rounded" style="width: 40px; height: 30px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#viewModal_<?= $row_id; ?>">
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <!-- Pemicu Modal Diperbarui dengan Penamaan ID Modal yang lebih aman (pakai underscore) -->
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" data-bs-toggle="modal" data-bs-target="#viewModal_<?= $row_id; ?>">View</button>
                                            
                                            <a href="edit.php?id=<?= $row_id; ?>" class="btn btn-sm btn-outline-primary px-2 py-1">Edit</a>
                                            <a href="delete.php?id=<?= $row_id; ?>" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="return confirm('Hapus data ini?');">Hapus</a>
                                        </td>
                                    </tr>

                                    <?php 
                                    // POP-UP VIEW
                                    ob_start(); 
                                    ?>
                                    <!-- Modal ID disesuaikan -->
                                    <div class="modal fade" id="viewModal_<?= $row_id; ?>" tabindex="-1" aria-labelledby="viewModalLabel_<?= $row_id; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                                                    <h5 class="modal-title fw-bold fs-5" id="viewModalLabel_<?= $row_id; ?>">Detail Informasi Kunjungan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row gx-5">
                                                        <div class="col-md-7">
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">ID Kunjungan</div>
                                                                <div class="col-8 fw-bold"><?= $formatted_id; ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Nama Siswa/Wali</div>
                                                                <div class="col-8"><?= htmlspecialchars($nama); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Asal Sekolah</div>
                                                                <div class="col-8"><?= htmlspecialchars($asal); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">No. HP / WA</div>
                                                                <div class="col-8"><?= htmlspecialchars($hp); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Sosial Media</div>
                                                                <div class="col-8"><?= renderSosmedLink($sosmed); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Waktu Kunjungan</div>
                                                                <div class="col-8"><?= date('d M Y / H:i', strtotime($waktu)); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Jenis Kunjungan</div>
                                                                <div class="col-8"><?= htmlspecialchars($jenis); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">PIC (Staf)</div>
                                                                <div class="col-8">Admin Sekolah</div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Alamat</div>
                                                                <div class="col-8"><?= htmlspecialchars($alamat); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Hasil Kunjungan</div>
                                                                <div class="col-8"><?= htmlspecialchars($hasil); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4 text-muted fw-medium">Status</div>
                                                                <div class="col-8">
                                                                    <span class="badge bg-<?= $badge_color; ?> px-3 py-2 rounded-pill"><?= htmlspecialchars($status); ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-5 d-flex flex-column align-items-center mt-4 mt-md-0">
                                                            <span class="text-muted fw-medium mb-3">Dokumentasi Kunjungan</span>
                                                            <div class="w-100 d-flex justify-content-center">
                                                                <?php if (!empty($foto) && file_exists('../../uploads/' . $foto)): ?>
                                                                    <img src="../../uploads/<?= htmlspecialchars($foto); ?>" alt="Dokumentasi" class="img-fluid rounded shadow-sm" style="width: 100%; max-height: 350px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="border rounded bg-light w-100 d-flex justify-content-center align-items-center" style="height: 250px;">
                                                                        <span class="text-muted small">Tidak ada foto</span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $modals_html .= ob_get_clean(); ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data kunjungan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $modals_html; ?>
<?php require_once '../../views/layouts/footer.php'; ?>