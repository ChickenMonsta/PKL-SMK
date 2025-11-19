<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAdmin();

// Ambil data jurusan
$stmt = $pdo->query("SELECT * FROM jurusan ORDER BY created_at DESC");
$jurusan = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <a href="index.php?page=admin&section=manage-jurusan" class="admin-nav-item active">
            <i class="fas fa-book"></i>
            <span class="nav-text">Manajemen Jurusan</span>
        </a>
        <a href="index.php?page=admin&section=manage-pendaftaran" class="admin-nav-item">
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
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Jurusan</h1>
            <p class="text-gray-600">Kelola program jurusan dan kuota pendaftaran PKL</p>
        </div>
        <div class="flex space-x-3">
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors flex items-center" data-bs-toggle="modal" data-bs-target="#tambahJurusanModal">
                <i class="fas fa-plus mr-2"></i>Tambah Jurusan
            </button>
            <button id="sidebarToggle" class="lg:hidden bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Total Jurusan</p>
                    <div class="stats-number"><?= count($jurusan) ?></div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Jurusan Aktif</p>
                    <div class="stats-number">
                        <?= count(array_filter($jurusan, function($j) { return $j['status'] == 'active'; })) ?>
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
                    <p class="text-gray-600 font-semibold">Total Kuota</p>
                    <div class="stats-number">
                        <?= array_sum(array_column($jurusan, 'kuota')) ?>
                    </div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Kuota Terisi</p>
                    <div class="stats-number">
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_pkl WHERE status = 'diterima'");
                        $total_diterima = $stmt->fetch()['total'];
                        echo $total_diterima;
                        ?>
                    </div>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-check text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Jurusan -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Program Jurusan</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table id="tabelJurusan" class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Kode</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama Jurusan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Kuota</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($jurusan)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                Belum ada data jurusan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jurusan as $j): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-2 py-1 rounded">
                                    <?= htmlspecialchars($j['kode_jurusan']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-800"><?= htmlspecialchars($j['nama_jurusan']) ?></div>
                            </td>
                            <td class="px-4 py-4 text-gray-600">
                                <?= htmlspecialchars(substr($j['deskripsi'] ?? 'Tidak ada deskripsi', 0, 50)) ?>...
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <span class="font-semibold text-gray-800 mr-2"><?= $j['kuota'] ?></span>
                                    <span class="text-sm text-gray-500">siswa</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <?= getStatusBadge($j['status']) ?>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <button type="button" class="btn-edit edit-jurusan-btn"
                                            data-id="<?= $j['id'] ?>"
                                            data-kode="<?= htmlspecialchars($j['kode_jurusan']) ?>"
                                            data-nama="<?= htmlspecialchars($j['nama_jurusan']) ?>"
                                            data-deskripsi="<?= htmlspecialchars($j['deskripsi'] ?? '') ?>"
                                            data-kuota="<?= $j['kuota'] ?>"
                                            data-status="<?= $j['status'] ?>">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <?php if ($j['status'] == 'active'): ?>
                                        <button type="button" class="btn-delete nonaktif-jurusan-btn" 
                                                data-id="<?= $j['id'] ?>">
                                            <i class="fas fa-times mr-1"></i>Nonaktifkan
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-success aktif-jurusan-btn" 
                                                data-id="<?= $j['id'] ?>">
                                            <i class="fas fa-check mr-1"></i>Aktifkan
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

<!-- Modal Tambah Jurusan -->
<div class="modal fade" id="tambahJurusanModal" tabindex="-1" aria-labelledby="tambahJurusanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahJurusanModalLabel">
                    <i class="fas fa-plus-circle text-yellow-600 mr-2"></i>
                    Tambah Jurusan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahJurusan" method="POST">
                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="kode_jurusan" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Kode Jurusan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" id="kode_jurusan" name="kode_jurusan" 
                                       placeholder="Contoh: RPL, TKJ" maxlength="10" required>
                                <p class="text-xs text-gray-500 mt-1">Kode unik untuk jurusan (maks. 10 karakter)</p>
                            </div>
                            
                            <div>
                                <label for="nama_jurusan" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Jurusan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" id="nama_jurusan" name="nama_jurusan" 
                                       placeholder="Masukkan nama jurusan lengkap" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="deskripsi" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Jurusan
                            </label>
                            <textarea class="form-input" id="deskripsi" name="deskripsi" rows="4" 
                                      placeholder="Deskripsi singkat tentang program jurusan..."></textarea>
                        </div>
                        
                        <div>
                            <label for="kuota" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Kuota Siswa <span class="text-red-500">*</span>
                            </label>
                            <input type="number" class="form-input" id="kuota" name="kuota" 
                                   min="1" value="10" required>
                            <p class="text-xs text-gray-500 mt-1">Jumlah maksimal siswa yang dapat mendaftar</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 px-6 py-4 border-t">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-2 rounded-lg transition-colors" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Jurusan -->
<div class="modal fade" id="editJurusanModal" tabindex="-1" aria-labelledby="editJurusanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editJurusanModalLabel">
                    <i class="fas fa-edit text-yellow-600 mr-2"></i>
                    Edit Data Jurusan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditJurusan" method="POST">
                <input type="hidden" id="edit_id_jurusan" name="id">
                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_kode_jurusan" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Kode Jurusan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" id="edit_kode_jurusan" name="kode_jurusan" required>
                            </div>
                            
                            <div>
                                <label for="edit_nama_jurusan" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Jurusan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" id="edit_nama_jurusan" name="nama_jurusan" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="edit_deskripsi" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Jurusan
                            </label>
                            <textarea class="form-input" id="edit_deskripsi" name="deskripsi" rows="4"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_kuota" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Kuota Siswa <span class="text-red-500">*</span>
                                </label>
                                <input type="number" class="form-input" id="edit_kuota" name="kuota" min="1" required>
                            </div>
                            
                            <div>
                                <label for="edit_status_jurusan" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select class="form-input" id="edit_status_jurusan" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 px-6 py-4 border-t">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-2 rounded-lg transition-colors" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/js/manage-jurusan.js"></script>