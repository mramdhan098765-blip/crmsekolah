<?php
// dashboard.php
require_once 'config/database.php';
session_start();

// Proteksi Halaman: Jika belum login, kembalikan ke halaman login
if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

// Ambil data statistik ringkas dari database
try {
    $total_sekolah = $pdo->query("SELECT COUNT(*) FROM sekolah")->fetchColumn();
    $total_kunjungan = $pdo->query("SELECT COUNT(*) FROM kunjungan")->fetchColumn();
    $total_siswa = $pdo->query("SELECT COUNT(*) FROM crm_siswa")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (PDOException $e) {
    // Jika tabel belum ada / error, atur nilai default ke 0
    $total_sekolah = 0;
    $total_kunjungan = 0;
    $total_siswa = 0;
    $total_users = 0;
}

// Memanggil Layout Header
require_once 'views/layouts/header.php';
?>

<div class="d-flex">
    <!-- Memanggil Layout Sidebar -->
    <?php require_once 'views/layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <div id="content" class="container-fluid p-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">Dashboard</h2>
                <p class="text-muted">Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['user']['nama'] ?? $_SESSION['user']['username'] ?? 'User'); ?></strong>!</p>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-semibold">Total Sekolah</h6>
                        <h2 class="display-6 fw-bold my-2"><?= $total_sekolah; ?></h2>
                        <a href="modules/sekolah/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-semibold">Total Kunjungan</h6>
                        <h2 class="display-6 fw-bold my-2"><?= $total_kunjungan; ?></h2>
                        <a href="modules/kunjungan/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-semibold">CRM Siswa</h6>
                        <h2 class="display-6 fw-bold my-2"><?= $total_siswa; ?></h2>
                        <a href="modules/crm_siswa/index.php" class="text-dark text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-semibold">Pengguna System</h6>
                        <h2 class="display-6 fw-bold my-2"><?= $total_users; ?></h2>
                        <a href="modules/users/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MULAI KODE GRAFIK ================= -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold">Grafik Ringkasan Sistem</h6>
                    </div>
                    <div class="card-body">
                        <!-- Canvas untuk Chart.js -->
                        <div style="height: 350px; width: 100%;">
                            <canvas id="dashboardChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Library Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('dashboardChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar', // Bisa diubah ke 'line', 'pie', dll
                data: {
                    labels: ['Total Sekolah', 'Total Kunjungan', 'CRM Siswa', 'Pengguna System'],
                    datasets: [{
                        label: 'Jumlah Data',
                        // Mengambil data langsung dari PHP
                        data: [
                            <?= $total_sekolah; ?>, 
                            <?= $total_kunjungan; ?>, 
                            <?= $total_siswa; ?>, 
                            <?= $total_users; ?>
                        ],
                        backgroundColor: [
                            'rgba(13, 110, 253, 0.8)', // Primary / Biru
                            'rgba(25, 135, 84, 0.8)',  // Success / Hijau
                            'rgba(255, 193, 7, 0.8)',  // Warning / Kuning
                            'rgba(13, 202, 240, 0.8)'  // Info / Cyan
                        ],
                        borderColor: [
                            '#0d6efd',
                            '#198754',
                            '#ffc107',
                            '#0dcaf0'
                        ],
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 // Sumbu Y hanya menampilkan angka bulat (1, 2, 3...)
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // Menyembunyikan legend karena label sudah jelas
                        }
                    }
                }
            });
        });
        </script>
        <!-- ================= SELESAI KODE GRAFIK ================= -->

    </div>
</div>

<?php
// Memanggil Layout Footer
require_once 'views/layouts/footer.php';
?>