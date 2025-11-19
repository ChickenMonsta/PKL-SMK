<?php

require_once __DIR__ . '/../../bootstrap.php';
requireSiswa();

$user_id = $_SESSION['user_id'];

// Pastikan session nama_lengkap ada
if (!isset($_SESSION['nama_lengkap'])) {
    // Ambil data user dari database jika tidak ada di session
    $stmt = $pdo->prepare("SELECT nama_lengkap FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();
    $_SESSION['nama_lengkap'] = $user_data['nama_lengkap'] ?? 'Siswa';
}

// Ambil data pendaftaran siswa
$stmt = $pdo->prepare("
    SELECT p.*, j.nama_jurusan, pr.nama_perusahaan 
    FROM pendaftaran_pkl p 
    JOIN jurusan j ON p.jurusan_id = j.id 
    JOIN perusahaan pr ON p.perusahaan_id = pr.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC 
    LIMIT 1
");
$stmt->execute([$user_id]);
$pendaftaran_terakhir = $stmt->fetch();

// Ambil data user lengkap
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();
?>

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Siswa</h1>
                    <p class="text-gray-600">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>!</p>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php?page=siswa&section=pendaftaran" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Daftar PKL
                    </a>
                    <a href="logout.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Status Pendaftaran</p>
                        <div class="text-2xl font-bold text-gray-800 mt-2">
                            <?php if ($pendaftaran_terakhir): ?>
                                <?= getStatusBadge($pendaftaran_terakhir['status']) ?>
                            <?php else: ?>
                                <span class="text-gray-500">Belum Mendaftar</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Jurusan</p>
                        <div class="text-2xl font-bold text-gray-800 mt-2">
                            <?php if ($pendaftaran_terakhir): ?>
                                <?= htmlspecialchars($pendaftaran_terakhir['nama_jurusan']) ?>
                            <?php else: ?>
                                <span class="text-gray-500">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Perusahaan</p>
                        <div class="text-2xl font-bold text-gray-800 mt-2">
                            <?php if ($pendaftaran_terakhir): ?>
                                <?= htmlspecialchars($pendaftaran_terakhir['nama_perusahaan']) ?>
                            <?php else: ?>
                                <span class="text-gray-500">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-building text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Pendaftaran Terakhir -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Pendaftaran Terakhir</h2>
                    
                    <?php if ($pendaftaran_terakhir): ?>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Tanggal Daftar</label>
                                    <p class="text-gray-800"><?= formatDateTime($pendaftaran_terakhir['created_at']) ?></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Status</label>
                                    <p><?= getStatusBadge($pendaftaran_terakhir['status']) ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Periode PKL</label>
                                    <p class="text-gray-800">
                                        <?= formatDate($pendaftaran_terakhir['tanggal_mulai']) ?> - 
                                        <?= formatDate($pendaftaran_terakhir['tanggal_selesai']) ?>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Jurusan</label>
                                    <p class="text-gray-800"><?= htmlspecialchars($pendaftaran_terakhir['nama_jurusan']) ?></p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Perusahaan</label>
                                <p class="text-gray-800 font-semibold"><?= htmlspecialchars($pendaftaran_terakhir['nama_perusahaan']) ?></p>
                            </div>
                            
                            <?php if (!empty($pendaftaran_terakhir['alasan_pkl'])): ?>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Alasan PKL</label>
                                <p class="text-gray-800 mt-1"><?= nl2br(htmlspecialchars($pendaftaran_terakhir['alasan_pkl'])) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($pendaftaran_terakhir['catatan_admin'])): ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <label class="text-sm font-medium text-gray-600">Catatan Admin</label>
                                <p class="text-gray-800 mt-1"><?= nl2br(htmlspecialchars($pendaftaran_terakhir['catatan_admin'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-gray-400 text-5xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Pendaftaran</h3>
                            <p class="text-gray-500 mb-4">Anda belum melakukan pendaftaran PKL</p>
                            <a href="index.php?page=siswa&section=pendaftaran" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors inline-block">
                                <i class="fas fa-plus mr-2"></i>Daftar Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Siswa -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Informasi Siswa</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user_data['nama_lengkap']) ?></h3>
                            <p class="text-gray-600"><?= htmlspecialchars($user_data['nis'] ?? 'Tidak ada NIS') ?></p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="text-gray-800"><?= htmlspecialchars($user_data['email']) ?></p>
                        </div>
                        
                        <?php if (!empty($user_data['no_telepon'])): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-600">No. Telepon</label>
                            <p class="text-gray-800"><?= htmlspecialchars($user_data['no_telepon']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($user_data['alamat'])): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Alamat</label>
                            <p class="text-gray-800"><?= htmlspecialchars($user_data['alamat']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <a href="index.php?page=siswa&section=profile" class="text-yellow-600 hover:text-yellow-700 font-semibold flex items-center">
                            <i class="fas fa-edit mr-2"></i>Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="index.php?page=siswa&section=pendaftaran" class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar PKL</h3>
                    <i class="fas fa-plus text-yellow-600 text-xl group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-gray-600 text-sm">Ajukan pendaftaran PKL baru</p>
            </a>
            
            <a href="index.php?page=siswa&section=riwayat" class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Riwayat</h3>
                    <i class="fas fa-history text-blue-600 text-xl group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-gray-600 text-sm">Lihat riwayat pendaftaran</p>
            </a>
            
            <a href="index.php?page=siswa&section=profile" class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Profil</h3>
                    <i class="fas fa-user-edit text-green-600 text-xl group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-gray-600 text-sm">Kelola data profil</p>
            </a>
            
            <a href="index.php?page=home" class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Beranda</h3>
                    <i class="fas fa-home text-purple-600 text-xl group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-gray-600 text-sm">Kembali ke beranda</p>
            </a>
        </div>
    </div>
</div>