<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAdmin();

$user_id = $_SESSION['user_id'];

// Ambil statistik untuk dashboard admin
$stats = [
    'total_siswa' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn(),
    'total_pendaftaran' => $pdo->query("SELECT COUNT(*) FROM pendaftaran_pkl")->fetchColumn(),
    'pending_pendaftaran' => $pdo->query("SELECT COUNT(*) FROM pendaftaran_pkl WHERE status = 'pending'")->fetchColumn(),
    'total_perusahaan' => $pdo->query("SELECT COUNT(*) FROM perusahaan WHERE status = 'active'")->fetchColumn(),
    'total_jurusan' => $pdo->query("SELECT COUNT(*) FROM jurusan WHERE status = 'active'")->fetchColumn()
];

// Ambil pendaftaran terbaru
$stmt = $pdo->query("
    SELECT p.*, u.nama_lengkap, j.nama_jurusan, pr.nama_perusahaan 
    FROM pendaftaran_pkl p 
    JOIN users u ON p.user_id = u.id 
    JOIN jurusan j ON p.jurusan_id = j.id 
    JOIN perusahaan pr ON p.perusahaan_id = pr.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
$pendaftaran_terbaru = $stmt->fetchAll();
?>

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
                    <p class="text-gray-600">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>!</p>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php?page=admin&section=manage-pendaftaran" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-tasks mr-2"></i>Kelola Pendaftaran
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Total Siswa</p>
                        <div class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_siswa'] ?></div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Total Pendaftaran</p>
                        <div class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_pendaftaran'] ?></div>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Pending</p>
                        <div class="text-3xl font-bold text-yellow-600 mt-2"><?= $stats['pending_pendaftaran'] ?></div>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Perusahaan</p>
                        <div class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_perusahaan'] ?></div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-building text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Jurusan</p>
                        <div class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_jurusan'] ?></div>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendaftaran Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Pendaftaran Terbaru</h2>
                
                <?php if ($pendaftaran_terbaru): ?>
                    <div class="space-y-4">
                        <?php foreach ($pendaftaran_terbaru as $pendaftaran): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($pendaftaran['nama_lengkap']) ?></h3>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($pendaftaran['nama_jurusan']) ?> - <?= htmlspecialchars($pendaftaran['nama_perusahaan']) ?></p>
                                    </div>
                                    <div>
                                        <?= getStatusBadge($pendaftaran['status']) ?>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= formatDateTime($pendaftaran['created_at']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="index.php?page=admin&section=manage-pendaftaran" class="text-blue-600 hover:text-blue-700 font-semibold">
                            Lihat Semua Pendaftaran →
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                        <p>Belum ada pendaftaran</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h2>
                
                <div class="grid grid-cols-1 gap-4">
                    <a href="index.php?page=admin&section=manage-pendaftaran" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-tasks text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Kelola Pendaftaran</h3>
                            <p class="text-sm text-gray-600">Verifikasi pendaftaran PKL siswa</p>
                        </div>
                    </a>
                    
                    <a href="index.php?page=admin&section=manage-perusahaan" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-200 transition-colors">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-building text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Kelola Perusahaan</h3>
                            <p class="text-sm text-gray-600">Tambah/edit perusahaan PKL</p>
                        </div>
                    </a>
                    
                    <a href="index.php?page=admin&section=manage-jurusan" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-200 transition-colors">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-book text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Kelola Jurusan</h3>
                            <p class="text-sm text-gray-600">Tambah/edit jurusan</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>