<?php
// modules/crm_siswa/delete.php
require_once '../../config/database.php';
require_once '../../config/auth.php';
checkAuth();

$id = $_GET['id'] ?? null;
if ($id) {
    // Data siswa dihapus. Jika ingin mengamankan log tahapan (siswa_tahap) relasinya sudah ON DELETE CASCADE di SQL.
    $stmt = $pdo->prepare("DELETE FROM crm_siswa WHERE id_calonsiswa = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;