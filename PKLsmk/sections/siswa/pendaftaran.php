<?php
require_once __DIR__ . '/../../bootstrap.php';
requireSiswa();


$user_id = $_SESSION['user_id'];

// Ambil data jurusan aktif
$stmt = $pdo->query("SELECT * FROM jurusan WHERE status = 'active' ORDER BY nama_jurusan");
$jurusan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data perusahaan aktif
$stmt = $pdo->query("SELECT * FROM perusahaan WHERE status = 'active' ORDER BY nama_perusahaan");
$perusahaan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cek apakah siswa sudah memiliki pendaftaran aktif
$stmt = $pdo->prepare("SELECT id FROM pendaftaran_pkl WHERE user_id = ? AND status IN ('pending', 'diterima')");
$stmt->execute([$user_id]);
$pendaftaran_aktif = $stmt->fetch();

// Ambil data user untuk prefill form
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
                    <h1 class="text-2xl font-bold text-gray-800">Form Pendaftaran PKL</h1>
                    <p class="text-gray-600">Isi formulir pendaftaran Praktik Kerja Lapangan</p>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php?page=siswa&section=dashboard" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <?php if ($pendaftaran_aktif): ?>
        <!-- Warning jika sudah ada pendaftaran aktif -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Pendaftaran Aktif Ditemukan</h3>
                    <p class="text-yellow-700">
                        Anda sudah memiliki pendaftaran PKL yang sedang diproses atau telah diterima. 
                        Silakan tunggu hingga pendaftaran sebelumnya selesai diproses atau hubungi admin 
                        untuk informasi lebih lanjut.
                    </p>
                    <div class="mt-4">
                        <a href="index.php?page=siswa&section=dashboard" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                            <i class="fas fa-eye mr-2"></i>Lihat Status Pendaftaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Form Pendaftaran -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Formulir Pendaftaran PKL</h2>
                    <p class="text-yellow-100 text-sm">Lengkapi semua data dengan benar</p>
                </div>
                
                <form id="formPendaftaranPKL" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Data Pribadi -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                            Data Pribadi
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" value="<?= htmlspecialchars($user_data['nama_lengkap']) ?>" readonly>
                            </div>
                            
                            <div>
                                <label class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    NIS <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" value="<?= htmlspecialchars($user_data['nis'] ?? 'Belum diisi') ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" class="form-input" value="<?= htmlspecialchars($user_data['email']) ?>" readonly>
                            </div>
                            
                            <div>
                                <label class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    No. Telepon
                                </label>
                                <input type="text" class="form-input" value="<?= htmlspecialchars($user_data['no_telepon'] ?? 'Belum diisi') ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="index.php?page=siswa&section=profile" class="text-yellow-600 hover:text-yellow-700 font-semibold text-sm flex items-center">
                                <i class="fas fa-edit mr-1"></i>Edit profil jika data tidak sesuai
                            </a>
                        </div>
                    </div>

                    <!-- Pilihan Jurusan dan Perusahaan -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-briefcase text-green-600 mr-2"></i>
                            Pilihan PKL
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="jurusan_id" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Pilih Jurusan <span class="text-red-500">*</span>
                                </label>
                                <select class="form-input" id="jurusan_id" name="jurusan_id" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    <?php foreach ($jurusan as $j): ?>
                                    <option value="<?= $j['id'] ?>" data-kuota="<?= $j['kuota'] ?>">
                                        <?= htmlspecialchars($j['nama_jurusan']) ?> (Kuota: <?= $j['kuota'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1" id="info-kuota"></p>
                            </div>
                            
                            <div>
                                <label for="perusahaan_id" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Pilih Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <select class="form-input" id="perusahaan_id" name="perusahaan_id" required>
                                    <option value="">-- Pilih Perusahaan --</option>
                                    <?php foreach ($perusahaan as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['nama_perusahaan']) ?> - <?= htmlspecialchars($p['kontak']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Periode PKL -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                            Periode PKL
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_mulai" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" class="form-input" id="tanggal_mulai" name="tanggal_mulai" required min="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <div>
                                <label for="tanggal_selesai" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" class="form-input" id="tanggal_selesai" name="tanggal_selesai" required>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <p class="text-sm text-gray-600" id="info-durasi"></p>
                        </div>
                    </div>

                    <!-- Alasan PKL -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-comment-alt text-orange-600 mr-2"></i>
                            Alasan Pemilihan PKL
                        </h3>
                        
                        <div>
                            <label for="alasan_pkl" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Jelaskan alasan Anda memilih program PKL ini <span class="text-red-500">*</span>
                            </label>
                            <textarea class="form-input" id="alasan_pkl" name="alasan_pkl" rows="4" 
                                      placeholder="Ceritakan mengapa Anda tertarik dengan program PKL ini, tujuan yang ingin dicapai, dan bagaimana ini akan membantu perkembangan karir Anda..." 
                                      required></textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimal 100 karakter</p>
                        </div>
                    </div>

                    <!-- Upload Berkas -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-upload text-red-600 mr-2"></i>
                            Upload Berkas
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="berkas_cv" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    CV/Resume
                                </label>
                                <input type="file" class="form-input" id="berkas_cv" name="berkas_cv" accept=".pdf,.doc,.docx">
                                <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX (Maks. 5MB)</p>
                            </div>
                            
                            <div>
                                <label for="berkas_portofolio" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Portofolio (Opsional)
                                </label>
                                <input type="file" class="form-input" id="berkas_portofolio" name="berkas_portofolio" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, PNG (Maks. 5MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Penting -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Penting
                        </h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Pastikan semua data yang diisi sudah benar dan valid</li>
                            <li>• Pendaftaran akan diverifikasi oleh admin sebelum diproses</li>
                            <li>• Status pendaftaran dapat dilihat di dashboard siswa</li>
                            <li>• Hubungi admin jika ada pertanyaan atau kendala</li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="button" onclick="history.back()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-8 py-3 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="../../assets/js/pendaftaran-siswa.js"></script>