<?php
// Header untuk halaman public (home)
?>
<header class="bg-white shadow-lg sticky top-0 z-50">
    <nav class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">PKL SMK Negeri 7</h1>
                    <p class="text-xs text-gray-600">Batam</p>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php?page=home" class="text-gray-700 hover:text-yellow-600 font-medium transition-colors duration-300">Beranda</a>
                <a href="#tentang" class="text-gray-700 hover:text-yellow-600 font-medium transition-colors duration-300">Tentang</a>
                <a href="#alur" class="text-gray-700 hover:text-yellow-600 font-medium transition-colors duration-300">Alur Pendaftaran</a>
                <a href="#kontak" class="text-gray-700 hover:text-yellow-600 font-medium transition-colors duration-300">Kontak</a>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=dashboard" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="logout.php" class="text-gray-700 hover:text-red-600 font-medium transition-colors duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-700 hover:text-yellow-600 font-medium transition-colors duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="register.php" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-user-plus mr-2"></i>Daftar
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <button class="md:hidden text-gray-700 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden mt-4 hidden" id="mobileMenu">
            <div class="flex flex-col space-y-4">
                <a href="index.php?page=home" class="text-gray-700 hover:text-yellow-600 font-medium">Beranda</a>
                <a href="#tentang" class="text-gray-700 hover:text-yellow-600 font-medium">Tentang</a>
                <a href="#alur" class="text-gray-700 hover:text-yellow-600 font-medium">Alur Pendaftaran</a>
                <a href="#kontak" class="text-gray-700 hover:text-yellow-600 font-medium">Kontak</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="text-gray-700 hover:text-yellow-600 font-medium">Login</a>
                    <a href="register.php" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium text-center">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<script>
    // Mobile menu toggle
    document.querySelector('button.md-hidden').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    });
</script>