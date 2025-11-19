<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $nis = trim($_POST['nis'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    
    $errors = [];
    
    // Validasi
    if (empty($username)) {
        $errors[] = 'Username harus diisi';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username minimal 3 karakter';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid';
    }
    
    if (empty($password)) {
        $errors[] = 'Password harus diisi';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Konfirmasi password tidak sesuai';
    }
    
    if (empty($nama_lengkap)) {
        $errors[] = 'Nama lengkap harus diisi';
    }
    
    if (empty($errors)) {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $errors[] = 'Username atau email sudah terdaftar';
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, password, email, nama_lengkap, nis, alamat, no_telepon, role) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'siswa')
                ");
                $stmt->execute([$username, $hashed_password, $email, $nama_lengkap, $nis, $alamat, $no_telepon]);
                
                // Get the new user ID
                $user_id = $pdo->lastInsertId();
                
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, 'REGISTER', 'User registrasi baru', $_SERVER['REMOTE_ADDR']]);
                
                $_SESSION['success_message'] = 'Pendaftaran berhasil! Silakan login.';
                header('Location: login.php');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem PKL SMK Negeri 7</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center mb-4">
                <span class="text-white font-bold text-2xl">S7</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Daftar Akun Baru</h2>
            <p class="mt-2 text-sm text-gray-600">
                Sistem Pendaftaran PKL SMK Negeri 7
            </p>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                    <input id="username" name="username" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Masukkan username">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Masukkan email">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Minimal 6 karakter">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password *</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Ulangi password">
                </div>
                
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                    <input id="nama_lengkap" name="nama_lengkap" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Masukkan nama lengkap">
                </div>
                
                <div>
                    <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                    <input id="nis" name="nis" type="text" 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Nomor Induk Siswa">
                </div>
                
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"
                              class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Masukkan alamat lengkap"></textarea>
                </div>
                
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                    <input id="no_telepon" name="no_telepon" type="tel" 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Contoh: 081234567890">
                </div>
            </div>

            <div class="flex items-center">
                <input id="agree_terms" name="agree_terms" type="checkbox" required
                       class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                <label for="agree_terms" class="ml-2 block text-sm text-gray-900">
                    Saya menyetujui 
                    <a href="#" class="text-yellow-600 hover:text-yellow-500">syarat dan ketentuan</a>
                </label>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Daftar Akun
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="login.php" class="font-medium text-yellow-600 hover:text-yellow-500">
                        Masuk di sini
                    </a>
                </p>
                <a href="index.php" class="text-sm text-gray-600 hover:text-gray-500 mt-2 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke beranda
                </a>
            </div>
        </form>
    </div>

    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('border-red-500');
                this.classList.remove('border-green-500');
            } else if (confirmPassword) {
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } else {
                this.classList.remove('border-red-500', 'border-green-500');
            }
        });
    </script>
</body>
</html>