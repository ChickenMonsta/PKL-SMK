<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - Sistem PKL SMKN 7 Batam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .floating { animation: float 3s ease-in-out infinite; }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }
        .pulse-ring { animation: pulse-ring 2s ease-out infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Maintenance Icon -->
        <div class="text-center mb-8 floating">
            <div class="relative inline-block">
                <div class="absolute inset-0 bg-yellow-400 rounded-full opacity-30 pulse-ring"></div>
                <div class="relative bg-white rounded-full p-8 shadow-2xl">
                    <i class="fas fa-tools text-6xl text-yellow-500"></i>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Sedang Dalam Perbaikan
            </h1>
            <p class="text-xl text-gray-600 mb-6">
                Kami sedang melakukan pemeliharaan sistem untuk meningkatkan kualitas layanan
            </p>
            
            <!-- Status Details -->
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-8">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse"></div>
                    <span class="text-blue-700 font-semibold">Status: Maintenance Mode</span>
                </div>
                <p class="text-gray-700 text-sm">
                    <i class="fas fa-clock mr-2"></i>
                    Estimasi selesai: <strong>Segera</strong>
                </p>
            </div>

            <!-- Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <i class="fas fa-shield-alt text-3xl text-green-500 mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Data Aman</h3>
                    <p class="text-sm text-gray-600">Semua data Anda tetap aman dan terlindungi</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <i class="fas fa-rocket text-3xl text-purple-500 mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Peningkatan</h3>
                    <p class="text-sm text-gray-600">Kami sedang meningkatkan performa sistem</p>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="border-t pt-6">
                <p class="text-gray-600 mb-4">
                    Jika ada pertanyaan mendesak, silakan hubungi:
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="mailto:admin@smkn7batam.sch.id" 
                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-envelope mr-2"></i>
                        Email Support
                    </a>
                    <a href="tel:+62778123456" 
                       class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-phone mr-2"></i>
                        Call Center
                    </a>
                </div>
            </div>

            <!-- Refresh Button -->
            <div class="mt-8">
                <button onclick="location.reload()" 
                        class="px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold rounded-lg hover:from-purple-600 hover:to-pink-600 transition transform hover:scale-105 shadow-lg">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Muat Ulang Halaman
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white">
            <p class="text-sm opacity-90">
                © <?= date('Y') ?> SMK Negeri 7 Batam - Sistem PKL
            </p>
        </div>
    </div>

    <script>
        // Auto refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);

        // Add some interactive particle effects
        const body = document.body;
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.style.position = 'fixed';
            particle.style.width = Math.random() * 10 + 5 + 'px';
            particle.style.height = particle.style.width;
            particle.style.borderRadius = '50%';
            particle.style.background = 'rgba(255, 255, 255, 0.3)';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animation = `float ${Math.random() * 3 + 2}s ease-in-out infinite`;
            particle.style.animationDelay = Math.random() * 2 + 's';
            body.appendChild(particle);
        }
    </script>
</body>
</html>