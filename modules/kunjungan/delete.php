<?php
// modules/kunjungan/delete.php
require_once '../../config/database.php';
require_once '../../config/auth.php';
checkAuth();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM kunjungan WHERE id_kunjungan = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;