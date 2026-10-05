<?php
// modules/agent/delete.php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Cari nama kolom Primary Key secara otomatis
        $stmt_cols = $pdo->query("SHOW COLUMNS FROM agent");
        $columns = $stmt_cols->fetchAll();
        
        $pk_column = 'id';
        if (count($columns) > 0) {
            $pk_column = $columns[0]['Field'];
            foreach($columns as $col) {
                if($col['Key'] == 'PRI') {
                    $pk_column = $col['Field'];
                    break;
                }
            }
        }

        // Eksekusi penghapusan
        $stmt = $pdo->prepare("DELETE FROM agent WHERE $pk_column = :id");
        $stmt->execute([':id' => $id]);
        
    } catch (PDOException $e) {
        // Abaikan error jika data tidak ditemukan atau query gagal
    }
}

header("Location: index.php?status=success_delete");
exit;