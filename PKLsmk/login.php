<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Username harus diisi';
    }
    
    if (empty($password)) {
        $errors[] = 'Password harus diisi';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], 'LOGIN', 'User login', $_SERVER['REMOTE_ADDR']]);
                
                header('Location: index.php?page=dashboard');
                exit();
            } else {
                $errors[] = 'Username atau password salah';
            }
        } catch (PDOException $e) {
            $errors[] = 'Terjadi kesalahan sistem';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem PKL SMK Negeri 7 Batam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0066CC 0%, #FF6B35 100%);
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 login-card rounded-2xl shadow-2xl p-8">
        <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full flex items-center justify-center mb-4 shadow-lg">
                <span class="text-white font-bold text-2xl">S7</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Masuk ke Akun</h2>
            <p class="mt-2 text-sm text-gray-600">
                Sistem Pendaftaran PKL SMK Negeri 7 Batam
            </p>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 animate-pulse">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                <div class="text-sm text-red-700">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username atau Email</label>
                    <div class="mt-1 relative">
                        <input id="username" name="username" type="text" required 
                               class="appearance-none relative block w-full px-3 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 transition-all duration-300"
                               placeholder="Masukkan username atau email">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" required 
                               class="appearance-none relative block w-full px-3 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 transition-all duration-300"
                               placeholder="Masukkan password">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Ingat saya
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Lupa password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-orange-500 hover:from-blue-700 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-white"></i>
                    </span>
                    Masuk
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                        Daftar di sini
                    </a>
                </p>
                <a href="index.php" class="text-sm text-gray-600 hover:text-gray-500 mt-2 inline-block transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke beranda
                </a>
            </div>
        </form>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-blue-500');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-blue-500');
                });
            });
        });
    </script>
</body>
</html>