<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAdmin();

// Ambil data pendaftaran dengan join
$stmt = $pdo->query("
    SELECT p.*, u.nama_lengkap, u.nis, u.email, j.nama_jurusan, pr.nama_perusahaan 
    FROM pendaftaran_pkl p 
    JOIN users u ON p.user_id = u.id 
    JOIN jurusan j ON p.jurusan_id = j.id 
    JOIN perusahaan pr ON p.perusahaan_id = pr.id 
    ORDER BY p.created_at DESC
");
$pendaftaran = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-sidebar">
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center">
                <i class="fas fa-user-shield text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-white font-bold text-lg">Admin Panel</h2>
                <p class="text-gray-300 text-sm"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
            </div>
        </div>
    </div>
    
    <nav class="py-4">
        <a href="index.php?page=admin&section=dashboard" class="admin-nav-item">
            <i class="fas fa-tachometer-alt"></i>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="index.php?page=admin&section=manage-perusahaan" class="admin-nav-item">
            <i class="fas fa-building"></i>
            <span class="nav-text">Manajemen Perusahaan</span>
        </a>
        <a href="index.php?page=admin&section=manage-jurusan" class="admin-nav-item">
            <i class="fas fa-book"></i>
            <span class="nav-text">Manajemen Jurusan</span>
        </a>
        <a href="index.php?page=admin&section=manage-pendaftaran" class="admin-nav-item active">
            <i class="fas fa-file-alt"></i>
            <span class="nav-text">Manajemen Pendaftaran</span>
        </a>
        <a href="logout.php" class="admin-nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-text">Logout</span>
        </a>
    </nav>
</div>

<div class="admin-main">
    <div class="admin-header">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pendaftaran PKL</h1>
            <p class="text-gray-600">Verifikasi dan kelola pendaftaran PKL siswa</p>
        </div>
        <button id="sidebarToggle" class="lg:hidden bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Filter Pendaftaran</h2>
            <div class="flex flex-wrap gap-3">
                <select id="filterStatus" class="form-input w-auto">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
                <select id="filterJurusan" class="form-input w-auto">
                    <option value="">Semua Jurusan</option>
                    <?php
                    $stmt = $pdo->query("SELECT DISTINCT j.id, j.nama_jurusan FROM jurusan j ORDER BY j.nama_jurusan");
                    $jurusan_options = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($jurusan_options as $j): ?>
                    <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nama_jurusan']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button id="resetFilter" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Total Pendaftaran</p>
                    <div class="stats-number"><?= count($pendaftaran) ?></div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Menunggu</p>
                    <div class="stats-number">
                        <?= count(array_filter($pendaftaran, function($p) { return $p['status'] == 'pending'; })) ?>
                    </div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Diterima</p>
                    <div class="stats-number">
                        <?= count(array_filter($pendaftaran, function($p) { return $p['status'] == 'diterima'; })) ?>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Ditolak</p>
                    <div class="stats-number">
                        <?= count(array_filter($pendaftaran, function($p) { return $p['status'] == 'ditolak'; })) ?>
                    </div>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pendaftaran -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Pendaftaran PKL</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table id="tabelPendaftaran" class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Siswa</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">NIS</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Jurusan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Perusahaan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Periode</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($pendaftaran)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                Belum ada data pendaftaran
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendaftaran as $p): ?>
                        <tr class="hover:bg-gray-50 transition-colors" data-status="<?= $p['status'] ?>" data-jurusan="<?= $p['jurusan_id'] ?>">
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-yellow-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($p['nama_lengkap']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($p['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600"><?= htmlspecialchars($p['nis'] ?? '-') ?></td>
                            <td class="px-4 py-4">
                                <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-2 py-1 rounded">
                                    <?= htmlspecialchars($p['nama_jurusan']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-600"><?= htmlspecialchars($p['nama_perusahaan']) ?></td>
                            <td class="px-4 py-4 text-gray-600">
                                <?= formatDate($p['tanggal_mulai']) ?> - <?= formatDate($p['tanggal_selesai']) ?>
                            </td>
                            <td class="px-4 py-4">
                                <?= getStatusBadge($p['status']) ?>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <button type="button" class="btn-edit detail-pendaftaran-btn"
                                            data-id="<?= $p['id'] ?>">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </button>
                                    <?php if ($p['status'] == 'pending'): ?>
                                        <button type="button" class="btn-success terima-pendaftaran-btn" 
                                                data-id="<?= $p['id'] ?>">
                                            <i class="fas fa-check mr-1"></i>Terima
                                        </button>
                                        <button type="button" class="btn-delete tolak-pendaftaran-btn" 
                                                data-id="<?= $p['id'] ?>">
                                            <i class="fas fa-times mr-1"></i>Tolak
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Pendaftaran -->
<div class="modal fade" id="detailPendaftaranModal" tabindex="-1" aria-labelledby="detailPendaftaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailPendaftaranModalLabel">
                    <i class="fas fa-file-alt text-yellow-600 mr-2"></i>
                    Detail Pendaftaran PKL
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailPendaftaranContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer bg-gray-50 px-6 py-4 border-t">
                <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-2 rounded-lg transition-colors" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/manage-pendaftaran.js"></script>