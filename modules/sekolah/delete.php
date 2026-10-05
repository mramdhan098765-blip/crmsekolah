<?php
// modules/sekolah/delete.php
require_once '../../config/database.php';
require_once '../../config/auth.php';
checkAuth();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM sekolah WHERE id_sekolah = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;