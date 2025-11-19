<?php
require_once __DIR__ . '/../../bootstrap.php';
requireSiswa();

$user_id = $_SESSION['user_id'];

// Ambil riwayat pendaftaran
$stmt = $pdo->prepare("
    SELECT p.*, j.nama_jurusan, pr.nama_perusahaan 
    FROM pendaftaran_pkl p 
    JOIN jurusan j ON p.jurusan_id = j.id 
    JOIN perusahaan pr ON p.perusahaan_id = pr.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$riwayat = $stmt->fetchAll();
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Riwayat Pendaftaran</h1>
            <p class="text-gray-600">Lihat riwayat pendaftaran PKL Anda</p>
        </div>

        <!-- Riwayat Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <?php if ($riwayat): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Daftar</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jurusan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Perusahaan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Periode</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($riwayat as $r): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= formatDateTime($r['created_at']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($r['nama_jurusan']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($r['nama_perusahaan']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= formatDate($r['tanggal_mulai']) ?> - <?= formatDate($r['tanggal_selesai']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= getStatusBadge($r['status']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="lihatDetail(<?= $r['id'] ?>)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-history text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Riwayat</h3>
                    <p class="text-gray-500 mb-4">Anda belum melakukan pendaftaran PKL</p>
                    <a href="index.php?page=siswa&section=pendaftaran" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors inline-block">
                        <i class="fas fa-plus mr-2"></i>Daftar PKL
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function lihatDetail(id) {
    alert('Detail pendaftaran ID: ' + id);
    // Bisa diimplementasikan modal detail
}
</script>