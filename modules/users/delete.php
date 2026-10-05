<?php
// modules/users/delete.php
require_once '../../config/database.php';
require_once '../../config/auth.php';
checkAuth();

if ($_SESSION['user']['nama_role'] !== 'Administrator') {
    die("Akses Ditolak!");
}

$id = $_GET['id'] ?? null;
if ($id && $id != $_SESSION['user']['id_user']) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;