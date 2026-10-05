<?php
// modules/agent/edit.php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$error = '';
$agent = null;

try {
    // Deteksi kolom secara otomatis
    $stmt_cols = $pdo->query("SHOW COLUMNS FROM agent");
    $columns = $stmt_cols->fetchAll();
    
    $pk_column = $columns[0]['Field'];
    $col_names_only = [];
    foreach($columns as $col) {
        $col_names_only[] = $col['Field'];
        if($col['Key'] == 'PRI') {
            $pk_column = $col['Field'];
        }
    }

    $col_nama = in_array('nama_agent', $col_names_only) ? 'nama_agent' : 'nama';
    $col_telp = in_array('no_telp', $col_names_only) ? 'no_telp' : 'no_telepon';

    // Ambil data agent
    $stmt = $pdo->prepare("SELECT * FROM agent WHERE $pk_column = :id");
    $stmt->execute([':id' => $id]);
    $agent = $stmt->fetch();
    
} catch (PDOException $e) {
    $error = "Kesalahan database: " . $e->getMessage();
}

if (!$agent) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $no_telepon = trim($_POST['no_telepon']);
    $instansi = trim($_POST['instansi']);
    $status = $_POST['status'];

    if (empty($nama)) {
        $error = "Nama Agent wajib diisi!";
    } else {
        try {
            $sql = "UPDATE agent SET $col_nama = :nama, $col_telp = :no_telepon, instansi = :instansi, status = :status WHERE $pk_column = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama' => $nama,
                ':no_telepon' => $no_telepon,
                ':instansi' => $instansi,
                ':status' => $status,
                ':id' => $id
            ]);
            header("Location: index.php?status=success_edit");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}

require_once '../../views/layouts/header.php';
?>

<div class="d-flex">
    <?php require_once '../../views/layouts/sidebar.php'; ?>

    <div id="content" class="container-fluid p-4">
        <div class="row mb-3">
            <div class="col">
                <h3 class="fw-bold">Edit Agent</h3>
            </div>
            <div class="col text-end">
                <a href="index.php" class="btn btn-secondary">&larr; Kembali</a>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Agent <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($agent[$col_nama] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($agent[$col_telp] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instansi / Asal</label>
                        <input type="text" name="instansi" class="form-control" value="<?= htmlspecialchars($agent['instansi'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Aktif" <?= (isset($agent['status']) && $agent['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="Nonaktif" <?= (isset($agent['status']) && $agent['status'] == 'Nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning px-4">Update Agent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>