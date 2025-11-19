<?php
// Perbaiki path untuk public home
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';

// Ambil data jurusan untuk ditampilkan
try {
    $stmt = $pdo->query("SELECT * FROM jurusan WHERE status = 'active' ORDER BY nama_jurusan");
    $jurusan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jurusan = [];
}

// Ambil data perusahaan aktif
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM perusahaan WHERE status = 'active'");
    $total_perusahaan = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $total_perusahaan = 0;
}

// Ambil data siswa
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'siswa'");
    $total_siswa = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $total_siswa = 0;
}

// Data galeri
$gallery_items = [
    [
        'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80',
        'title' => 'Workshop Programming',
        'description' => 'Siswa RPL sedang mengikuti workshop programming'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1535223289827-42f1e9919769?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
        'title' => 'Praktik Jaringan',
        'description' => 'Siswa TKJ melakukan praktik konfigurasi jaringan'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2064&q=80',
        'title' => 'Desain Multimedia',
        'description' => 'Siswa MM membuat karya desain grafis'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1581093458791-375db59396ba?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2067&q=80',
        'title' => 'Praktik Otomotif',
        'description' => 'Siswa TKRO belajar service kendaraan'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1559028012-481c04fa702d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80',
        'title' => 'Bengkel Motor',
        'description' => 'Siswa TBSM praktik service sepeda motor'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
        'title' => 'Presentasi Project',
        'description' => 'Siswa mempresentasikan project akhir'
    ]
];
?>

<!-- Hero Section dengan animasi -->
<section class="hero">
    <div class="container mx-auto px-4">
        <div class="hero-content text-center text-white">
            <h1 class="text-5xl lg:text-7xl font-bold mb-6 leading-tight slide-in">
                Program <span class="gradient-text">PKL</span><br>
                <span class="text-4xl lg:text-6xl">SMK Negeri 7 Batam</span>
            </h1>
            <p class="text-xl lg:text-2xl mb-8 max-w-3xl mx-auto leading-relaxed slide-in">
                Mempersiapkan generasi unggul siap kerja melalui pengalaman industri langsung 
                dengan program Praktik Kerja Lapangan yang berkualitas.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center slide-in">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=dashboard" class="btn-primary text-lg px-8 py-4">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Saya
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn-primary text-lg px-8 py-4">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                    </a>
                    <a href="login.php" class="bg-white text-gray-800 hover:bg-gray-100 font-semibold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                <?php endif; ?>
                <a href="#jurusan" class="border-2 border-white text-white hover:bg-white hover:text-gray-800 font-semibold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 smooth-scroll">
                    <i class="fas fa-book mr-2"></i>Lihat Jurusan
                </a>
            </div>
        </div>
    </div>
    
    <!-- Floating Shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    
    <!-- Particles -->
    <div class="particles"></div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
        <div class="flex flex-col items-center">
            <span class="text-white text-sm mb-2">Scroll untuk menjelajahi</span>
            <div class="w-6 h-10 border-2 border-white rounded-full flex justify-center">
                <div class="w-1 h-3 bg-white rounded-full mt-2 animate-bounce"></div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center slide-in">
                <div class="counter text-6xl font-bold mb-2" data-count="<?= $total_siswa ?>">0</div>
                <p class="text-xl text-gray-600 font-semibold">Siswa Terdaftar</p>
            </div>
            <div class="text-center slide-in">
                <div class="counter text-6xl font-bold mb-2" data-count="<?= count($jurusan) ?>">0</div>
                <p class="text-xl text-gray-600 font-semibold">Program Jurusan</p>
            </div>
            <div class="text-center slide-in">
                <div class="counter text-6xl font-bold mb-2" data-count="<?= $total_perusahaan ?>">0</div>
                <p class="text-xl text-gray-600 font-semibold">Perusahaan Mitra</p>
            </div>
        </div>
    </div>
</section>

<!-- Jurusan Section -->
<section id="jurusan" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="section-title text-4xl font-bold text-center text-gray-800 mb-4">Program Jurusan</h2>
        <p class="text-xl text-gray-600 text-center mb-16 max-w-2xl mx-auto">
            Pilih program jurusan yang sesuai dengan minat dan bakat Anda untuk memulai perjalanan karir yang sukses.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($jurusan)): ?>
                <div class="col-span-3 text-center py-12">
                    <i class="fas fa-book text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-600 mb-2">Belum Ada Jurusan</h3>
                    <p class="text-gray-500">Data jurusan akan segera tersedia</p>
                </div>
            <?php else: ?>
                <?php foreach ($jurusan as $index => $j): ?>
                <div class="jurusan-card bg-white rounded-2xl shadow-xl p-6 card-hover slide-in" data-animate>
                    <div class="jurusan-content">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-orange-500 rounded-xl flex items-center justify-center mb-4 mx-auto">
                            <?php
                            $icons = [
                                'RPL' => 'fa-laptop-code',
                                'TKJ' => 'fa-network-wired',
                                'MM' => 'fa-palette',
                                'TKRO' => 'fa-car',
                                'TBSM' => 'fa-motorcycle'
                            ];
                            $icon = $icons[$j['kode_jurusan']] ?? 'fa-book';
                            ?>
                            <i class="fas <?= $icon ?> text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 text-center mb-3"><?= htmlspecialchars($j['nama_jurusan']) ?></h3>
                        <p class="text-gray-600 text-center mb-4 line-clamp-3">
                            <?= htmlspecialchars($j['deskripsi'] ?? 'Program jurusan unggulan dengan kurikulum terkini.') ?>
                        </p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-users mr-1"></i>
                                Kuota: <?= $j['kuota'] ?> siswa
                            </span>
                            <span class="text-sm text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>
                                Tersedia
                            </span>
                        </div>
                        <button onclick="showJurusanDetail(<?= $j['id'] ?>)" 
                                class="w-full bg-gradient-to-r from-blue-600 to-orange-500 hover:from-blue-700 hover:to-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-info-circle mr-2"></i>Detail Jurusan
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Alur Pendaftaran Section -->
<section id="alur" class="py-20 bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <div class="container mx-auto px-4">
        <h2 class="section-title text-4xl font-bold text-center text-white mb-4">Alur Pendaftaran PKL</h2>
        <p class="text-xl text-gray-300 text-center mb-16 max-w-2xl mx-auto">
            Ikuti langkah-langkah mudah berikut untuk mendaftar program PKL di SMK Negeri 7 Batam
        </p>
        
        <div class="timeline">
            <div class="timeline-item left">
                <div class="timeline-content">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">1</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Registrasi Akun</h3>
                    </div>
                    <p class="text-gray-600">Daftar akun siswa dengan mengisi data pribadi yang valid melalui form registrasi online.</p>
                </div>
            </div>
            <div class="timeline-item right">
                <div class="timeline-content">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">2</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Login & Isi Form</h3>
                    </div>
                    <p class="text-gray-600">Masuk ke sistem dan lengkapi formulir pendaftaran PKL dengan data yang diperlukan.</p>
                </div>
            </div>
            <div class="timeline-item left">
                <div class="timeline-content">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">3</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Pilih Jurusan & Perusahaan</h3>
                    </div>
                    <p class="text-gray-600">Pilih program jurusan dan perusahaan tujuan PKL yang sesuai dengan minat dan kemampuan.</p>
                </div>
            </div>
            <div class="timeline-item right">
                <div class="timeline-content">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">4</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Upload Berkas</h3>
                    </div>
                    <p class="text-gray-600">Upload CV, portofolio, dan dokumen pendukung lainnya yang diperlukan.</p>
                </div>
            </div>
            <div class="timeline-item left">
                <div class="timeline-content">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">5</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Tunggu Konfirmasi</h3>
                    </div>
                    <p class="text-gray-600">Admin akan memverifikasi dan mengkonfirmasi pendaftaran dalam 3-5 hari kerja.</p>
                </div>
            </div>
            <div class="timeline-item right">
                <div class="timeline-content">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">6</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Mulai PKL</h3>
                    </div>
                    <p class="text-gray-600">Setelah diterima, siswa dapat memulai program PKL di perusahaan yang telah ditentukan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="section-title text-4xl font-bold text-center text-gray-800 mb-4">Galeri Kegiatan</h2>
        <p class="text-xl text-gray-600 text-center mb-16 max-w-2xl mx-auto">
            Dokumentasi berbagai kegiatan pembelajaran dan praktik siswa SMK Negeri 7 Batam
        </p>
        
        <div class="gallery-grid">
            <?php foreach ($gallery_items as $index => $item): ?>
            <div class="gallery-item slide-in" data-animate>
                <img src="<?= $item['image'] ?>" alt="<?= $item['title'] ?>" class="w-full h-64 object-cover">
                <div class="gallery-overlay">
                    <h3 class="text-lg font-bold text-white mb-2"><?= $item['title'] ?></h3>
                    <p class="text-white text-sm"><?= $item['description'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="#" class="btn-primary px-8 py-3">
                <i class="fas fa-images mr-2"></i>Lihat Galeri Lengkap
            </a>
        </div>
    </div>
</section>

<!-- Testimoni Section -->
<section class="py-20 bg-gradient-to-br from-blue-50 to-orange-50">
    <div class="container mx-auto px-4">
        <h2 class="section-title text-4xl font-bold text-center text-gray-800 mb-4">Apa Kata Mereka?</h2>
        <p class="text-xl text-gray-600 text-center mb-16 max-w-2xl mx-auto">
            Pengalaman langsung dari siswa yang telah mengikuti program PKL
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover slide-in" data-animate>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Ahmad Rizki</h4>
                        <p class="text-sm text-gray-600">Siswa RPL - PT. Teknologi Indonesia</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">
                    "Program PKL di PT. Teknologi Indonesia memberikan pengalaman berharga dalam pengembangan software. Saya belajar banyak tentang industri IT yang sesungguhnya."
                </p>
                <div class="flex text-yellow-400 mt-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover slide-in" data-animate>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Siti Aminah</h4>
                        <p class="text-sm text-gray-600">Siswa TKJ - PT. Network Systems</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">
                    "PKL di bidang jaringan komputer membuka wawasan saya tentang pentingnya infrastruktur IT. Mentor di perusahaan sangat membantu dalam proses belajar."
                </p>
                <div class="flex text-yellow-400 mt-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover slide-in" data-animate>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Budi Santoso</h4>
                        <p class="text-sm text-gray-600">Siswa MM - Studio Kreatif Media</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">
                    "Pengalaman PKL di studio kreatif sangat menyenangkan. Saya bisa mengaplikasikan ilmu desain grafis secara langsung untuk klien nyata."
                </p>
                <div class="flex text-yellow-400 mt-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Pendaftaran Section -->
<section id="pendaftaran" class="py-20 bg-gradient-to-r from-blue-600 to-orange-500">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Siap Memulai Pengalaman PKL?</h2>
        <p class="text-xl text-white mb-8 max-w-2xl mx-auto opacity-90">
            Daftarkan diri Anda sekarang dan dapatkan pengalaman berharga di dunia industri yang sesungguhnya. 
            Bangun karir masa depan Anda bersama SMK Negeri 7 Batam.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=dashboard" class="bg-white text-gray-800 hover:bg-gray-100 font-bold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg text-lg">
                    <i class="fas fa-rocket mr-2"></i>Mulai Pendaftaran
                </a>
                <a href="index.php?page=dashboard" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-800 font-bold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 text-lg">
                    <i class="fas fa-eye mr-2"></i>Lihat Status
                </a>
            <?php else: ?>
                <a href="register.php" class="bg-white text-gray-800 hover:bg-gray-100 font-bold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg text-lg">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </a>
                <a href="login.php" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-800 font-bold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 text-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            <?php endif; ?>
        </div>
        <p class="text-white mt-6 opacity-80">
            Butuh bantuan? <a href="#kontak" class="underline font-semibold hover:opacity-80 smooth-scroll">Hubungi kami</a>
        </p>
    </div>
</section>

<!-- Kontak Section -->
<section id="kontak" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="section-title text-4xl font-bold text-center text-gray-800 mb-4">Kontak Kami</h2>
        <p class="text-xl text-gray-600 text-center mb-16 max-w-2xl mx-auto">
            Hubungi kami untuk informasi lebih lanjut tentang program PKL SMK Negeri 7 Batam
        </p>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Informasi Kontak</h3>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Alamat</h4>
                            <p class="text-gray-600">Jl. Pendidikan No. 123, Batu Aji, Batam, Kepulauan Riau 29432</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Telepon</h4>
                            <p class="text-gray-600">(0778) 1234-5678</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Email</h4>
                            <p class="text-gray-600">info@smkn7batam.sch.id</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Jam Operasional</h4>
                            <p class="text-gray-600">Senin - Jumat: 07:30 - 16:00 WIB</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Follow Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center text-white hover:bg-pink-700 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white hover:bg-red-700 transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Kirim Pesan</h3>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" placeholder="Masukkan nama lengkap" class="form-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                            <input type="email" placeholder="Masukkan alamat email" class="form-input" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subjek Pesan</label>
                        <input type="text" placeholder="Subjek pesan" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                        <textarea placeholder="Tulis pesan Anda di sini..." rows="5" class="form-input" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-96 w-full bg-gradient-to-br from-blue-100 to-orange-100 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-map-marked-alt text-6xl text-blue-600 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Lokasi SMK Negeri 7 Batam</h3>
                    <p class="text-gray-600">Jl. Pendidikan No. 123, Batu Aji, Batam</p>
                    <a href="#" class="inline-block mt-4 btn-primary">
                        <i class="fas fa-directions mr-2"></i>Lihat di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>