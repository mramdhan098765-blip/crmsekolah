<?php
// views/layouts/sidebar.php
$uri = $_SERVER['REQUEST_URI'];
?>
<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3">
    <div class="text-center mb-4">
        <h5 class="fw-bold text-white fs-6">CRM SEKOLAH</h5>
        <small class="text-muted"><?= htmlspecialchars($_SESSION['user']['nama']) ?></small>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="<?= strpos($uri, 'dashboard.php') !== false ? 'active' : '' ?>" href="/crmsekolah/dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="<?= strpos($uri, 'kunjungan') !== false ? 'active' : '' ?>" href="/crmsekolah/modules/kunjungan/index.php">
                <i class="bi bi-journal-check me-2"></i> Data Kunjungan
            </a>
        </li>
        <li class="nav-item">
            <a class="<?= strpos($uri, 'crm_siswa') !== false ? 'active' : '' ?>" href="/crmsekolah/modules/crm_siswa/index.php">
                <i class="bi bi-people me-2"></i> CRM Calon Siswa
            </a>
        </li>
        <li class="nav-item">
            <a class="<?= strpos($uri, 'sekolah') !== false ? 'active' : '' ?>" href="/crmsekolah/modules/sekolah/index.php">
                <i class="bi bi-building me-2"></i> Data Sekolah
            </a>
        </li>
        
        <?php if (hasRole(['Administrator'])): ?>
        <hr class="text-secondary my-2">
        <small class="text-uppercase text-muted px-2" style="font-size: 10px;">Pengaturan Admin</small>
        <li class="nav-item mt-1">
            <a class="<?= strpos($uri, 'users') !== false ? 'active' : '' ?>" href="/crmsekolah/modules/users/index.php">
                <i class="bi bi-person-gear me-2"></i> Kelola User & Role
            </a>
        </li>
        <li class="nav-item">
            <a class="<?= strpos($uri, 'agent') !== false ? 'active' : '' ?>" href="/crmsekolah/modules/agent/index.php">
                <i class="bi bi-person-badge me-2"></i> Kelola Agent
            </a>
        </li>
        <li class="nav-item">
            <a class="<?= strpos($uri, 'label_status') !== false ? 'active' : '' ?>" href="/crmsekolah/modules/label_status/index.php">
                <i class="bi bi-tags me-2"></i> Label Status
            </a>
        </li>
        <li class="nav-item">
            <a class="<?= strpos($uri, 'pengaturan') !== false ? 'active' : '' ?>" href="/crmsekolah/modules/pengaturan/index.php">
                <i class="bi bi-gear me-2"></i> Pengaturan
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>