<footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-16 transform transition-all duration-500">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 mb-12">
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-4 mb-6 transform transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-2xl">S7</span>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">SMK NEGERI 7</h2>
                        <p class="text-sm text-gray-400 font-medium mt-1">Pendaftaran PKL</p>
                    </div>
                </div>
                <p class="text-gray-300 text-lg leading-relaxed mb-6 max-w-md">
                    Mempersiapkan generasi unggul siap kerja melalui program PKL berkualitas dengan pengalaman industri langsung.
                </p>
                <div class="flex space-x-5">
                    <a href="#" class="social-icon w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center transform transition-all duration-300 hover:bg-yellow-500 hover:scale-110 hover:rotate-12">
                        <i class="fab fa-facebook-f text-white text-lg"></i>
                    </a>
                    <a href="#" class="social-icon w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center transform transition-all duration-300 hover:bg-yellow-500 hover:scale-110 hover:rotate-12">
                        <i class="fab fa-twitter text-white text-lg"></i>
                    </a>
                    <a href="#" class="social-icon w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center transform transition-all duration-300 hover:bg-yellow-500 hover:scale-110 hover:rotate-12">
                        <i class="fab fa-instagram text-white text-lg"></i>
                    </a>
                    <a href="#" class="social-icon w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center transform transition-all duration-300 hover:bg-yellow-500 hover:scale-110 hover:rotate-12">
                        <i class="fab fa-youtube text-white text-lg"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-6 text-white relative inline-block">
                    Tautan Cepat
                    <span class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-yellow-500 transform transition-all duration-300"></span>
                </h3>
                <ul class="space-y-3">
                    <?php
                    $links = [
                        'Beranda' => 'index.php?page=home',
                        'Jurusan' => 'index.php?page=home#jurusan',
                        'Alur Pendaftaran' => 'index.php?page=home#alur',
                        'Galeri' => 'index.php?page=home#gallery',
                        'Pendaftaran' => 'index.php?page=home#pendaftaran'
                    ];
                    foreach($links as $text => $url):
                    ?>
                    <li>
                        <a href="<?= $url ?>" class="text-gray-300 hover:text-yellow-400 transition-all duration-300 transform hover:translate-x-2 flex items-center group">
                            <i class="fas fa-chevron-right text-yellow-500 text-xs mr-3 transform transition-all duration-300 group-hover:translate-x-1"></i>
                            <?= $text ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-6 text-white relative inline-block">
                    Kontak
                    <span class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-yellow-500 transform transition-all duration-300"></span>
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-start space-x-4 group transform transition-all duration-300 hover:translate-x-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0 transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-12">
                            <i class="fas fa-map-marker-alt text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm leading-relaxed">Jl. Pendidikan No. 123, Kota Bandung, Jawa Barat 40123</p>
                        </div>
                    </li>
                    <li class="flex items-center space-x-4 group transform transition-all duration-300 hover:translate-x-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0 transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-12">
                            <i class="fas fa-phone text-white text-sm"></i>
                        </div>
                        <span class="text-gray-300">(022) 1234-5678</span>
                    </li>
                    <li class="flex items-center space-x-4 group transform transition-all duration-300 hover:translate-x-2">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0 transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-12">
                            <i class="fas fa-envelope text-white text-sm"></i>
                        </div>
                        <span class="text-gray-300">info@smkn7bandung.sch.id</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-700 pt-8 text-center">
            <p class="text-gray-400 text-sm">
                &copy; 2024 SMK Negeri 7 Bandung. All rights reserved. | 
                <span class="text-yellow-400">Membangun Generasi Unggul untuk Indonesia</span>
            </p>
        </div>
    </div>
</footer>