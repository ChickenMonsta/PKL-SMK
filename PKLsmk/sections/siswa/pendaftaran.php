<?php
// Pastikan file bootstrap.php ada dan berfungsi
require_once __DIR__ . '/../../bootstrap.php';
// Pastikan fungsi ini memuat data siswa (termasuk NIS) ke dalam $_SESSION
requireSiswa(); 

$user_id = $_SESSION['user_id'];

/*
// --- DEBUG SESSION (AKTIFKAN JIKA NIS TIDAK MUNCUL) ---
echo '<pre style="background: #FFFBEA; border: 1px solid #FFE0B2; padding: 10px; color: #333;">';
echo '<strong>DEBUG SESSION:</strong><br>';
if (isset($_SESSION['nis'])) {
    echo 'NIS ditemukan: ' . htmlspecialchars($_SESSION['nis']) . '<br>';
} else {
    echo 'NIS TIDAK DITEMUKAN dalam session. Cek file login/requireSiswa().<br>';
}
echo 'Nama Lengkap: ' . htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Kosong') . '<br>';
echo 'User ID: ' . htmlspecialchars($_SESSION['user_id'] ?? 'Kosong') . '<br>';
echo '</pre>';
// --- DEBUG SESSION END ---
*/

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
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-yellow-500 px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pendaftaran PKL</h1>
                        <p class="text-gray-800">Isi formulir pendaftaran PKL dengan lengkap dan benar</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-30 rounded-full flex items-center justify-center shadow-md">
                        <i class="fas fa-file-alt text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <?php if ($pendaftaran_aktif): ?>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-6 mt-6 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-yellow-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-800">
                                Anda sudah memiliki pendaftaran dengan status: 
                                <?= getStatusBadge($pendaftaran_aktif['status']) ?>
                            </h3>
                            <p class="text-sm text-gray-700 mt-1">
                                Silakan tunggu konfirmasi dari admin atau periksa di dashboard Anda.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="p-6">
                <form id="formPendaftaran" action="process_pendaftaran.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-yellow-500 mr-3"></i>
                            Data Pribadi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" 
                                       value="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 bg-gray-100 rounded-lg transition-colors cursor-not-allowed"
                                       disabled>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIS</label>
                                <input type="text" 
                                       value="<?= htmlspecialchars($_SESSION['nis'] ?? '') ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 bg-gray-100 rounded-lg transition-colors cursor-not-allowed"
                                       disabled>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-briefcase text-yellow-500 mr-3"></i>
                            Data PKL
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jurusan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="jurusan_id" name="jurusan_id" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors">
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
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors">
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
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors"
                                           min="<?= date('Y-m-d') ?>">
                                </div>
                                
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Selesai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors"
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>
                            </div>
                            
                            <div>
                                <label for="alasan_pkl" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alasan Memilih PKL <span class="text-red-500">*</span>
                                </label>
                                <textarea id="alasan_pkl" name="alasan_pkl" required rows="4"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors"
                                            placeholder="Jelaskan alasan Anda memilih program PKL ini (minimal 10 karakter)..."></textarea>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span id="charCount">0</span> karakter
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-upload text-yellow-500 mr-3"></i>
                            Dokumen Pendukung (Opsional)
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    CV/Resume
                                </label>
                                <input type="file" name="berkas_cv" 
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors">
                                <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, JPG, PNG (Maks. 5MB)</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Portofolio
                                </label>
                                <input type="file" name="berkas_portofolio" 
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors">
                                <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, JPG, PNG (Maks. 5MB)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <a href="index.php?page=siswa" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                        
                        <button type="submit" 
                                class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-gray-900 rounded-lg transition-colors font-semibold flex items-center shadow-md"
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
    
    // 1. Cek Tanggal Selesai vs Mulai
    if (tanggalSelesai <= tanggalMulai) {
        e.preventDefault();
        alert('Tanggal selesai harus setelah tanggal mulai');
        return false;
    }
    
    // 2. Validasi minimal durasi PKL (30 hari)
    const oneDay = 1000 * 60 * 60 * 24;
    const diffTime = Math.abs(tanggalSelesai - tanggalMulai);
    const diffDays = Math.ceil(diffTime / oneDay);
    
    if (diffDays < 30) {
        e.preventDefault();
        alert('Durasi PKL minimal 30 hari');
        return false;
    }
    
    // 3. Validasi minimal karakter alasan
    if (alasanPkl.length < 10) {
        e.preventDefault();
        alert('Alasan PKL minimal 10 karakter');
        return false;
    }

    // Jika sudah memiliki pendaftaran aktif, batalkan submission form biasa
    <?php if ($pendaftaran_aktif): ?>
        e.preventDefault();
        alert('Anda sudah memiliki pendaftaran yang sedang diproses. Mohon tunggu konfirmasi.');
        return false;
    <?php endif; ?>
});

// AJAX form submission untuk feedback yang lebih baik
document.getElementById('formPendaftaran').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Cek lagi status pendaftaran aktif sebelum AJAX
    <?php if ($pendaftaran_aktif): ?>
        // Ini dicegah di validation di atas, tapi baik untuk redundant check
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
    .then(response => {
        // Cek response.ok sebelum mencoba response.json()
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Asumsi Swal.fire adalah SweetAlert2 atau library sejenis
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-yellow-500 hover:bg-yellow-600 text-gray-900' // Styling SweetAlert
                }
            }).then(() => {
                window.location.href = 'index.php?page=siswa'; // Redirect ke dashboard siswa
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
        // Penanganan error jaringan/server
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengirim formulir: ' + error.message,
            confirmButtonText: 'OK'
        });
        console.error('Fetch Error:', error);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        // Hanya re-enable jika tidak ada pendaftaran aktif (status tidak berubah)
        if (!<?= $pendaftaran_aktif ? 'true' : 'false' ?>) {
            submitBtn.disabled = false;
        }
    });
});
</script>