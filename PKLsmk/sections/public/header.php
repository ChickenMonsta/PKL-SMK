<!-- Header untuk Halaman Public -->
<header class="header-solid sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-yellow rounded-full flex items-center justify-center shadow">
                <span class="text-black font-bold text-lg">S7</span>
            </div>
            <div>
                <h1 class="text-xl font-bold text-black">SMK NEGERI 7 BATAM</h1>
                <p class="text-xs text-gray font-medium">Pendaftaran PKL</p>
            </div>
        </div>
        <nav class="hidden lg:block">
            <ul class="flex space-x-8">
                <li><a href="index.php?page=home" class="nav-link text-black hover:text-yellow font-semibold transition-all duration-300 relative group">
                    Beranda
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow transition-all duration-300 group-hover:w-full"></span>
                </a></li>
                <li><a href="#jurusan" class="nav-link text-black hover:text-yellow font-semibold transition-all duration-300 relative group smooth-scroll">
                    Jurusan
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow transition-all duration-300 group-hover:w-full"></span>
                </a></li>
                <li><a href="#alur" class="nav-link text-black hover:text-yellow font-semibold transition-all duration-300 relative group smooth-scroll">
                    Alur Pendaftaran
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow transition-all duration-300 group-hover:w-full"></span>
                </a></li>
                <li><a href="#gallery" class="nav-link text-black hover:text-yellow font-semibold transition-all duration-300 relative group smooth-scroll">
                    Galeri
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow transition-all duration-300 group-hover:w-full"></span>
                </a></li>
                <li><a href="#pendaftaran" class="nav-link text-black hover:text-yellow font-semibold transition-all duration-300 relative group smooth-scroll">
                    Pendaftaran
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow transition-all duration-300 group-hover:w-full"></span>
                </a></li>
                <li><a href="#kontak" class="nav-link text-black hover:text-yellow font-semibold transition-all duration-300 relative group smooth-scroll">
                    Kontak
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow transition-all duration-300 group-hover:w-full"></span>
                </a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php?page=dashboard" class="btn-primary px-6 py-2 text-sm">
                        Dashboard
                    </a></li>
                <?php else: ?>
                    <li><a href="login.php" class="text-black hover:text-yellow font-semibold transition-all duration-300 relative group">
                        Login
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow transition-all duration-300 group-hover:w-full"></span>
                    </a></li>
                    <li><a href="register.php" class="btn-primary px-6 py-2 text-sm">
                        Daftar
                    </a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <button class="lg:hidden text-black text-xl hover:text-yellow transition-colors" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Mobile Menu -->
    <div class="lg:hidden hidden bg-white-transparent border-t border-gray-transparent py-4 backdrop-blur-sm" id="mobileMenu">
        <div class="container mx-auto px-4 space-y-3">
            <a href="index.php?page=home" class="block text-black hover:text-yellow font-semibold transition duration-300 py-2">Beranda</a>
            <a href="#jurusan" class="block text-black hover:text-yellow font-semibold transition duration-300 py-2 smooth-scroll">Jurusan</a>
            <a href="#alur" class="block text-black hover:text-yellow font-semibold transition duration-300 py-2 smooth-scroll">Alur Pendaftaran</a>
            <a href="#gallery" class="block text-black hover:text-yellow font-semibold transition duration-300 py-2 smooth-scroll">Galeri</a>
            <a href="#pendaftaran" class="block text-black hover:text-yellow font-semibold transition duration-300 py-2 smooth-scroll">Pendaftaran</a>
            <a href="#kontak" class="block text-black hover:text-yellow font-semibold transition duration-300 py-2 smooth-scroll">Kontak</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=dashboard" class="block btn-primary text-center py-2">Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="block text-black hover:text-yellow font-semibold transition duration-300 py-2">Login</a>
                <a href="register.php" class="block btn-primary text-center py-2">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</header>