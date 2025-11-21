<!-- Hero Section -->
<section class="hero">
    <!-- Konten hero section sama seperti sebelumnya -->
    <div class="particles" id="particles-js"></div>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    
    <div class="container mx-auto px-4 hero-content">
        <div class="max-w-4xl">
            <h1 class="text-5xl md:text-7xl font-bold text-white mb-8 slide-in" data-animate="true">
                Program <span class="gradient-text">PKL</span> SMK Negeri 7
            </h1>
            <p class="text-2xl text-gray-200 mb-10 slide-in leading-relaxed" data-animate="true">
                Wadah pengembangan kompetensi siswa melalui pengalaman kerja langsung di industri. 
                <span class="block mt-2 text-yellow-300 font-semibold">Siapkan karir profesional Anda dengan program PKL terbaik.</span>
            </p>
            <div class="flex flex-col sm:flex-row gap-6 slide-in" data-animate="true">
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn-primary py-4 px-10 rounded-2xl font-bold text-xl inline-flex items-center justify-center pulse transform transition-all duration-300 hover:scale-105">
                        <span>Daftar Sekarang</span>
                        <i class="fas fa-arrow-right ml-3 text-lg transform transition-transform duration-300 group-hover:translate-x-1"></i>
                    </a>
                <?php else: ?>
                    <a href="index.php?page=dashboard" class="btn-primary py-4 px-10 rounded-2xl font-bold text-xl inline-flex items-center justify-center pulse transform transition-all duration-300 hover:scale-105">
                        <span>Ke Dashboard</span>
                        <i class="fas fa-tachometer-alt ml-3 text-lg"></i>
                    </a>
                <?php endif; ?>
                <a href="#jurusan" class="bg-transparent border-3 border-white text-white py-4 px-10 rounded-2xl font-bold text-xl inline-flex items-center justify-center hover:bg-white hover:text-gray-900 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <span>Jelajahi Jurusan</span>
                    <i class="fas fa-search ml-3 text-lg"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="absolute bottoam-8 left-1/2 transform -translate-x-1/2">
        <div class="floating">
            <a href="#jurusan" class="text-white text-3xl transform transition-all duration-300 hover:scale-125 hover:text-yellow-300">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="slide-in">
                <?php
                $total_siswa = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn();
                ?>
                <div class="counter" data-count="<?= $total_siswa ?>">0</div>
                <p class="text-gray-600 mt-2">Siswa Terdaftar</p>
            </div>
            <div class="slide-in">
                <?php
                $total_perusahaan = $pdo->query("SELECT COUNT(*) FROM perusahaan")->fetchColumn();
                ?>
                <div class="counter" data-count="<?= $total_perusahaan ?>">0</div>
                <p class="text-gray-600 mt-2">Perusahaan Mitra</p>
            </div>
            <div class="slide-in">
                <div class="counter" data-count="95">0</div>
                <p class="text-gray-600 mt-2">Kelulusan (%)</p>
            </div>
            <div class="slide-in">
                <?php
                $total_jurusan = $pdo->query("SELECT COUNT(*) FROM jurusan")->fetchColumn();
                ?>
                <div class="counter" data-count="<?= $total_jurusan ?>">0</div>
                <p class="text-gray-600 mt-2">Program Jurusan</p>
            </div>
        </div>
    </div>
</section>

<!-- Jurusan Section -->
<section id="jurusan" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 section-title">Program Jurusan</h2>
        <p class="text-gray-600 text-center max-w-2xl mx-auto mb-12">
            Pilih jurusan yang sesuai dengan minat dan bakat Anda. Setiap program dirancang untuk mempersiapkan siswa menghadapi dunia kerja.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $stmt = $pdo->query("SELECT * FROM jurusan");
            while($jurusan = $stmt->fetch()):
            ?>
            <div class="jurusan-card bg-white rounded-xl shadow-lg overflow-hidden card-hover" onclick="showJurusanDetail(<?= $jurusan['id'] ?>)">
                <div class="relative overflow-hidden">
                    <?php if($jurusan['gambar']): ?>
                        <img src="uploads/<?= $jurusan['gambar'] ?>" alt="<?= $jurusan['nama_jurusan'] ?>" class="w-full h-48 object-cover transition duration-500">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                             alt="<?= $jurusan['nama_jurusan'] ?>" class="w-full h-48 object-cover transition duration-500">
                    <?php endif; ?>
                    <div class="absolute top-4 right-4 bg-yellow-500 text-white py-1 px-3 rounded-full text-sm font-semibold">
                        Populer
                    </div>
                </div>
                <div class="p-6 jurusan-content">
                    <h3 class="text-xl font-bold mb-2 text-gray-800"><?= $jurusan['nama_jurusan'] ?></h3>
                    <p class="text-gray-600 mb-4"><?= substr($jurusan['deskripsi'], 0, 100) ?>...</p>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span><i class="fas fa-users mr-1"></i> <?= $jurusan['kuota'] ?> Kuota</span>
                        <span><i class="fas fa-star mr-1 text-yellow-500"></i> 4.8/5</span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Alur Pendaftaran Section -->
<section id="alur" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 section-title">Alur Pendaftaran</h2>
        <p class="text-gray-600 text-center max-w-2xl mx-auto mb-12">
            Ikuti langkah-langkah berikut untuk mendaftar program PKL SMK Negeri 7
        </p>
        
        <div class="timeline">
            <div class="timeline-item left slide-in">
                <div class="timeline-content card-hover">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold mr-3">1</div>
                        <h3 class="text-xl font-bold">Registrasi Akun</h3>
                    </div>
                    <p class="text-gray-600">Buat akun dengan menggunakan NIS dan email aktif Anda. Verifikasi email untuk mengaktifkan akun.</p>
                </div>
            </div>
            <div class="timeline-item right slide-in">
                <div class="timeline-content card-hover">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold mr-3">2</div>
                        <h3 class="text-xl font-bold">Isi Formulir Data Diri</h3>
                    </div>
                    <p class="text-gray-600">Lengkapi formulir data diri dengan informasi yang valid dan dapat dipertanggungjawabkan.</p>
                </div>
            </div>
            <div class="timeline-item left slide-in">
                <div class="timeline-content card-hover">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold mr-3">3</div>
                        <h3 class="text-xl font-bold">Pilih Jurusan & Perusahaan</h3>
                    </div>
                    <p class="text-gray-600">Pilih program jurusan dan perusahaan mitra yang sesuai dengan minat dan kompetensi Anda.</p>
                </div>
            </div>
            <div class="timeline-item right slide-in">
                <div class="timeline-content card-hover">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold mr-3">4</div>
                        <h3 class="text-xl font-bold">Upload Dokumen</h3>
                    </div>
                    <p class="text-gray-600">Upload dokumen pendukung seperti foto, KTP, dan surat keterangan lainnya yang diperlukan.</p>
                </div>
            </div>
            <div class="timeline-item left slide-in">
                <div class="timeline-content card-hover">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold mr-3">5</div>
                        <h3 class="text-xl font-bold">Konfirmasi Pendaftaran</h3>
                    </div>
                    <p class="text-gray-600">Tunggu konfirmasi dari admin dan lakukan pembayaran jika diperlukan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 section-title">Galeri Kegiatan PKL</h2>
        <p class="text-gray-600 text-center max-w-2xl mx-auto mb-12">
            Dokumentasi kegiatan siswa selama mengikuti program Praktik Kerja Lapangan
        </p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="gallery-item rounded-xl overflow-hidden shadow-md h-48">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80" 
                     alt="Kegiatan PKL" class="w-full h-full object-cover">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus text-white text-2xl"></i>
                </div>
            </div>
            <div class="gallery-item rounded-xl overflow-hidden shadow-md h-48">
                <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                     alt="Kegiatan PKL" class="w-full h-full object-cover">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus text-white text-2xl"></i>
                </div>
            </div>
            <div class="gallery-item rounded-xl overflow-hidden shadow-md h-48">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                     alt="Kegiatan PKL" class="w-full h-full object-cover">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus text-white text-2xl"></i>
                </div>
            </div>
            <div class="gallery-item rounded-xl overflow-hidden shadow-md h-48">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                     alt="Kegiatan PKL" class="w-full h-full object-cover">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pendaftaran Section -->
<section id="pendaftaran" class="py-20 bg-gray-900 text-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 section-title">Formulir Pendaftaran PKL</h2>
        <p class="text-gray-300 text-center max-w-2xl mx-auto mb-12">
            Isi formulir berikut dengan data yang valid untuk mendaftar program PKL
        </p>
        
        <div class="max-w-3xl mx-auto bg-gray-800 p-8 rounded-xl shadow-lg">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <div class="text-center py-12">
                    <i class="fas fa-lock text-yellow-500 text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold mb-4">Silakan Login Terlebih Dahulu</h3>
                    <p class="text-gray-300 mb-6">Untuk mendaftar PKL, Anda perlu login ke akun siswa terlebih dahulu.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="login.php" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                            Login Sekarang
                        </a>
                        <a href="register.php" class="bg-transparent border-2 border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                            Daftar Akun
                        </a>
                    </div>
                </div>
            <?php elseif($_SESSION['role'] != 'siswa'): ?>
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold mb-4">Akses Ditolak</h3>
                    <p class="text-gray-300 mb-6">Hanya siswa yang dapat mendaftar PKL.</p>
                    <a href="index.php?page=dashboard" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                        Kembali ke Dashboard
                    </a>
                </div>
            <?php else: ?>
                <?php
                // Cek apakah sudah mendaftar
                $stmt = $pdo->prepare("SELECT * FROM pendaftaran_pkl WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $existing_pendaftaran = $stmt->fetch();
                
                if($existing_pendaftaran): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-check-circle text-green-500 text-6xl mb-6"></i>
                        <h3 class="text-2xl font-bold mb-4">Anda Sudah Mendaftar PKL</h3>
                        <p class="text-gray-300 mb-6">Status pendaftaran Anda: 
                            <span class="font-bold <?= $existing_pendaftaran['status'] == 'diterima' ? 'text-green-500' : ($existing_pendaftaran['status'] == 'ditolak' ? 'text-red-500' : 'text-yellow-500') ?>">
                                <?= strtoupper($existing_pendaftaran['status']) ?>
                            </span>
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="index.php?page=siswa&section=status" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                                Lihat Status
                            </a>
                            <a href="index.php?page=dashboard" class="bg-transparent border-2 border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                                Ke Dashboard
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <form id="formPendaftaran" class="space-y-6" method="POST" action="process_pendaftaran.php">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama" class="block text-gray-200 font-medium mb-2">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" value="<?= $_SESSION['nama_lengkap'] ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" readonly>
                            </div>
                            <div>
                                <label for="nis" class="block text-gray-200 font-medium mb-2">NIS</label>
                                <?php
                                $stmt = $pdo->prepare("SELECT nis FROM users WHERE id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $user = $stmt->fetch();
                                ?>
                                <input type="text" id="nis" name="nis" value="<?= $user['nis'] ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" readonly>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="kelas" class="block text-gray-200 font-medium mb-2">Kelas</label>
                                <?php
                                $stmt = $pdo->prepare("SELECT kelas FROM users WHERE id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $user = $stmt->fetch();
                                ?>
                                <input type="text" id="kelas" name="kelas" value="<?= $user['kelas'] ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" readonly>
                            </div>
                            <div>
                                <label for="jurusan_id" class="block text-gray-200 font-medium mb-2">Jurusan</label>
                                <select id="jurusan_id" name="jurusan_id" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" required>
                                    <option value="">Pilih Jurusan</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM jurusan");
                                    while($jurusan = $stmt->fetch()):
                                    ?>
                                    <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama_jurusan'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="perusahaan_id" class="block text-gray-200 font-medium mb-2">Perusahaan Tempat PKL</label>
                            <select id="perusahaan_id" name="perusahaan_id" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" required>
                                <option value="">Pilih Perusahaan</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM perusahaan");
                                while($perusahaan = $stmt->fetch()):
                                ?>
                                <option value="<?= $perusahaan['id'] ?>"><?= $perusahaan['nama_perusahaan'] ?> - <?= $perusahaan['bidang'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tanggal_mulai" class="block text-gray-200 font-medium mb-2">Tanggal Mulai PKL</label>
                                <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" required>
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-gray-200 font-medium mb-2">Tanggal Selesai PKL</label>
                                <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="alamat_perusahaan" class="block text-gray-200 font-medium mb-2">Alamat Perusahaan</label>
                            <textarea id="alamat_perusahaan" name="alamat_perusahaan" rows="3" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" required></textarea>
                        </div>
                        
                        <div>
                            <label for="pembimbing_sekolah" class="block text-gray-200 font-medium mb-2">Nama Pembimbing Sekolah</label>
                            <input type="text" id="pembimbing_sekolah" name="pembimbing_sekolah" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input text-white" required>
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-gray-900 font-bold py-3 px-4 rounded-lg transition duration-300 shadow-lg hover:shadow-xl">
                                Daftar PKL
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Kontak Section -->
<section id="kontak" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 section-title">Kontak Kami</h2>
        <p class="text-gray-600 text-center max-w-2xl mx-auto mb-12">
            Hubungi kami jika Anda memiliki pertanyaan seputar program PKL
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <div class="text-center p-6 bg-gray-50 rounded-xl shadow-md card-hover">
                <div class="bg-yellow-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Alamat</h3>
                <p class="text-gray-600">Jl. Pendidikan No. 123, Kota Bandung, Jawa Barat</p>
            </div>
            <div class="text-center p-6 bg-gray-50 rounded-xl shadow-md card-hover">
                <div class="bg-yellow-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Telepon</h3>
                <p class="text-gray-600">(022) 1234567</p>
            </div>
            <div class="text-center p-6 bg-gray-50 rounded-xl shadow-md card-hover">
                <div class="bg-yellow-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-gray-800">Email</h3>
                <p class="text-gray-600">info@smkn7bandung.sch.id</p>
            </div>
        </div>
        
        <div class="mt-16 max-w-4xl mx-auto bg-gray-50 p-8 rounded-xl shadow-md">
            <h3 class="text-2xl font-bold mb-6 text-center">Kirim Pesan</h3>
            <form class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_pesan" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                        <input type="text" id="nama_pesan" name="nama_pesan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input" required>
                    </div>
                    <div>
                        <label for="email_pesan" class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" id="email_pesan" name="email_pesan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input" required>
                    </div>
                </div>
                <div>
                    <label for="subjek" class="block text-gray-700 font-medium mb-2">Subjek</label>
                    <input type="text" id="subjek" name="subjek" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input" required>
                </div>
                <div>
                    <label for="pesan" class="block text-gray-700 font-medium mb-2">Pesan</label>
                    <textarea id="pesan" name="pesan" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 form-input" required></textarea>
                </div>
                <div>
                    <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>