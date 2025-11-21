<?php
require_once __DIR__ . '/../../bootstrap.php';
requireSiswa();

$user_id = $_SESSION['user_id'];

// Ambil data jurusan dan perusahaan
$stmt = $pdo->query("SELECT * FROM jurusan WHERE status = 'active' ORDER BY nama_jurusan");
$jurusan = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM perusahaan WHERE status = 'active' ORDER BY nama_perusahaan");
$perusahaan = $stmt->fetchAll();

// Generate CSRF token
$csrf_token = generateCSRFToken();

// Cek apakah sudah ada pendaftaran aktif
$stmt = $pdo->prepare("SELECT status FROM pendaftaran_pkl WHERE user_id = ? AND status IN ('pending', 'diterima') ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$pendaftaran_aktif = $stmt->fetch();
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-orange-500 px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Pendaftaran PKL</h1>
                        <p class="text-blue-100">Isi formulir pendaftaran PKL dengan lengkap dan benar</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <?php if ($pendaftaran_aktif): ?>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-6 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Anda sudah memiliki pendaftaran dengan status: 
                                <?= getStatusBadge($pendaftaran_aktif['status']) ?>
                            </h3>
                            <p class="text-sm text-blue-700 mt-1">
                                Silakan tunggu konfirmasi dari admin atau periksa di dashboard Anda.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="p-6">
                <form id="formPendaftaran" action="process_pendaftaran.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Data Pribadi -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-blue-500 mr-3"></i>
                            Data Pribadi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" 
                                       value="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       disabled>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIS</label>
                                <input type="text" 
                                       value="<?= htmlspecialchars($_SESSION['nis'] ?? '') ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Data PKL -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-briefcase text-blue-500 mr-3"></i>
                            Data PKL
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jurusan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="jurusan_id" name="jurusan_id" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Pilih Jurusan</option>
                                        <?php foreach ($jurusan as $j): ?>
                                            <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nama_jurusan']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="perusahaan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Perusahaan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="perusahaan_id" name="perusahaan_id" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Pilih Perusahaan</option>
                                        <?php foreach ($perusahaan as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_perusahaan']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           min="<?= date('Y-m-d') ?>">
                                </div>
                                
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Selesai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>
                            </div>
                            
                            <div>
                                <label for="alasan_pkl" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alasan Memilih PKL <span class="text-red-500">*</span>
                                </label>
                                <textarea id="alasan_pkl" name="alasan_pkl" required rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                          placeholder="Jelaskan alasan Anda memilih program PKL ini (minimal 10 karakter)..."></textarea>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span id="charCount">0</span> karakter
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Pendukung -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-upload text-blue-500 mr-3"></i>
                            Dokumen Pendukung (Opsional)
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    CV/Resume
                                </label>
                                <input type="file" name="berkas_cv" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, JPG, PNG (Maks. 5MB)</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Portofolio
                                </label>
                                <input type="file" name="berkas_portofolio" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, JPG, PNG (Maks. 5MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <a href="index.php?page=siswa" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                        
                        <button type="submit" 
                                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold flex items-center"
                                <?= $pendaftaran_aktif ? 'disabled' : '' ?>>
                            <i class="fas fa-paper-plane mr-2"></i>
                            <?= $pendaftaran_aktif ? 'Sudah Mendaftar' : 'Ajukan Pendaftaran' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Character count for alasan_pkl
document.getElementById('alasan_pkl').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// Form validation
document.getElementById('formPendaftaran').addEventListener('submit', function(e) {
    const tanggalMulai = new Date(document.getElementById('tanggal_mulai').value);
    const tanggalSelesai = new Date(document.getElementById('tanggal_selesai').value);
    const alasanPkl = document.getElementById('alasan_pkl').value;
    
    if (tanggalSelesai <= tanggalMulai) {
        e.preventDefault();
        alert('Tanggal selesai harus setelah tanggal mulai');
        return false;
    }
    
    // Validasi minimal durasi PKL (30 hari)
    const diffTime = Math.abs(tanggalSelesai - tanggalMulai);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 30) {
        e.preventDefault();
        alert('Durasi PKL minimal 30 hari');
        return false;
    }
    
    if (alasanPkl.length < 10) {
        e.preventDefault();
        alert('Alasan PKL minimal 10 karakter');
        return false;
    }
});

// AJAX form submission
document.getElementById('formPendaftaran').addEventListener('submit', function(e) {
    e.preventDefault();
    
    <?php if ($pendaftaran_aktif): ?>
        alert('Anda sudah memiliki pendaftaran yang sedang diproses');
        return false;
    <?php endif; ?>
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
    submitBtn.disabled = true;
    
    fetch('process_pendaftaran.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php?page=siswa';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengirim formulir',
            confirmButtonText: 'OK'
        });
        console.error('Error:', error);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>