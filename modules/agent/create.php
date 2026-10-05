<?php
// modules/agent/create.php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $no_telepon = trim($_POST['no_telepon']);
    $instansi = trim($_POST['instansi']);
    $status = $_POST['status'];

    if (empty($nama)) {
        $error = "Nama Agent wajib diisi!";
    } else {
        try {
            // Deteksi nama kolom di tabel
            $stmt_cols = $pdo->query("SHOW COLUMNS FROM agent");
            $columns = $stmt_cols->fetchAll(PDO::FETCH_COLUMN);
            
            $col_nama = in_array('nama_agent', $columns) ? 'nama_agent' : 'nama';
            $col_telp = in_array('no_telp', $columns) ? 'no_telp' : 'no_telepon';

            $sql = "INSERT INTO agent ($col_nama, $col_telp, instansi, status) VALUES (:nama, :no_telepon, :instansi, :status)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama' => $nama,
                ':no_telepon' => $no_telepon,
                ':instansi' => $instansi,
                ':status' => $status
            ]);

            header("Location: index.php?status=success_add");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal menyimpan data: " . $e->getMessage();
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
                <h3 class="fw-bold">Tambah Agent Baru</h3>
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
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instansi / Asal</label>
                        <input type="text" name="instansi" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">Simpan Agent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>