<?php
require_once __DIR__ . '/../../../bootstrap.php';

if (!isAdmin()) {
    http_response_code(403);
    echo 'Unauthorized access';
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo 'ID pendaftaran tidak valid';
    exit();
}

$id = intval($_GET['id']);

try {
    // Get pendaftaran details with joins
    $stmt = $pdo->prepare("
        SELECT p.*, u.nama_lengkap, u.nis, u.email, u.alamat, u.no_telepon, 
               j.nama_jurusan, j.kode_jurusan, pr.nama_perusahaan, pr.alamat as alamat_perusahaan, pr.kontak
        FROM pendaftaran_pkl p 
        JOIN users u ON p.user_id = u.id 
        JOIN jurusan j ON p.jurusan_id = j.id 
        JOIN perusahaan pr ON p.perusahaan_id = pr.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $pendaftaran = $stmt->fetch();

    if (!$pendaftaran) {
        echo '<div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Data Tidak Ditemukan</h3>
                <p class="text-gray-600">Pendaftaran dengan ID tersebut tidak ditemukan.</p>
              </div>';
        exit();
    }

    // Format dates
    $tanggal_mulai = formatDate($pendaftaran['tanggal_mulai'], 'd F Y');
    $tanggal_selesai = formatDate($pendaftaran['tanggal_selesai'], 'd F Y');
    $created_at = formatDateTime($pendaftaran['created_at'], 'd F Y H:i');
    
    ?>
    <div class="space-y-6">
        <!-- Header Info -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($pendaftaran['nama_lengkap']) ?></h3>
                    <p class="text-gray-600">NIS: <?= htmlspecialchars($pendaftaran['nis'] ?? '-') ?></p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Tanggal Daftar</div>
                    <div class="font-semibold text-gray-800"><?= $created_at ?></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Data Siswa -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                    Data Siswa
                </h4>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
                        <p class="text-gray-800"><?= htmlspecialchars($pendaftaran['nama_lengkap']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">NIS</label>
                        <p class="text-gray-800"><?= htmlspecialchars($pendaftaran['nis'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <p class="text-gray-800"><?= htmlspecialchars($pendaftaran['email']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">No. Telepon</label>
                        <p class="text-gray-800"><?= htmlspecialchars($pendaftaran['no_telepon'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Alamat</label>
                        <p class="text-gray-800"><?= htmlspecialchars($pendaftaran['alamat'] ?? '-') ?></p>
                    </div>
                </div>
            </div>

            <!-- Data PKL -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-briefcase text-green-600 mr-2"></i>
                    Data PKL
                </h4>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Jurusan</label>
                        <p class="text-gray-800">
                            <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-2 py-1 rounded">
                                <?= htmlspecialchars($pendaftaran['kode_jurusan']) ?>
                            </span>
                            <?= htmlspecialchars($pendaftaran['nama_jurusan']) ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Perusahaan</label>
                        <p class="text-gray-800 font-semibold"><?= htmlspecialchars($pendaftaran['nama_perusahaan']) ?></p>
                        <p class="text-gray-600 text-sm"><?= htmlspecialchars($pendaftaran['alamat_perusahaan']) ?></p>
                        <p class="text-gray-600 text-sm">Kontak: <?= htmlspecialchars($pendaftaran['kontak']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Periode PKL</label>
                        <p class="text-gray-800"><?= $tanggal_mulai ?> - <?= $tanggal_selesai ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <p class="text-gray-800"><?= getStatusBadge($pendaftaran['status']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alasan PKL -->
        <?php if (!empty($pendaftaran['alasan_pkl'])): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-comment-alt text-purple-600 mr-2"></i>
                Alasan Memilih PKL
            </h4>
            <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($pendaftaran['alasan_pkl'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Berkas -->
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-alt text-orange-600 mr-2"></i>
                Berkas Pendaftaran
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">CV/Resume</label>
                    <div class="mt-1">
                        <?php if (!empty($pendaftaran['berkas_cv'])): ?>
                            <a href="../../uploads/<?= htmlspecialchars($pendaftaran['berkas_cv']) ?>" 
                               target="_blank" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-download mr-2"></i>
                                Download CV
                            </a>
                        <?php else: ?>
                            <span class="text-gray-500">Tidak ada berkas</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Portofolio</label>
                    <div class="mt-1">
                        <?php if (!empty($pendaftaran['berkas_portofolio'])): ?>
                            <a href="../../uploads/<?= htmlspecialchars($pendaftaran['berkas_portofolio']) ?>" 
                               target="_blank" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-download mr-2"></i>
                                Download Portofolio
                            </a>
                        <?php else: ?>
                            <span class="text-gray-500">Tidak ada berkas</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catatan Admin -->
        <?php if (!empty($pendaftaran['catatan_admin'])): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                Catatan Admin
            </h4>
            <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($pendaftaran['catatan_admin'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo '<div class="text-center py-8">
            <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Terjadi Kesalahan</h3>
            <p class="text-gray-600">Gagal memuat data pendaftaran: ' . $e->getMessage() . '</p>
          </div>';
}
?>