<?php
require_once '../../config/database.php';
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Query Delete (Hilangkan komentar untuk mengaktifkan fungsi ini ke database)
        // $stmt = $pdo->prepare("DELETE FROM label_status WHERE id = ?");
        // $stmt->execute([$id]);
        
        // Redirect dengan status sukses
        header("Location: index.php?status=success_delete");
        exit;
    } catch (PDOException $e) {
        // Handle jika data tidak bisa dihapus (misal karena berelasi dengan tabel siswa)
        echo "Error: Data tidak dapat dihapus karena masih digunakan.";
    }
} else {
    header("Location: index.php");
    exit;
}
?>