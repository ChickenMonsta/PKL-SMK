<?php
session_start();
// Pastikan kedua file ini (config.php dan database.php) sudah sesuai dengan perbaikan sebelumnya.
require_once 'includes/config.php';
require_once 'includes/database.php'; // Asumsi ini mendefinisikan $pdo

// Array untuk menyimpan nilai input POST agar form menjadi 'sticky'
$form_data = [
    'username' => '',
    'email' => '',
    'nama_lengkap' => '',
    'nis' => '',
    'alamat' => '',
    'no_telepon' => '',
    'agree_terms' => false,
];

// Redirect jika pengguna sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data POST dan masukkan ke $form_data
    $form_data['username'] = trim($_POST['username'] ?? '');
    $form_data['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $form_data['nama_lengkap'] = trim($_POST['nama_lengkap'] ?? '');
    $form_data['nis'] = trim($_POST['nis'] ?? '');
    $form_data['alamat'] = trim($_POST['alamat'] ?? '');
    $form_data['no_telepon'] = trim($_POST['no_telepon'] ?? '');
    $form_data['agree_terms'] = isset($_POST['agree_terms']);
    
    // --- Validasi Data ---
    
    // 1. Validasi Username
    if (empty($form_data['username'])) {
        $errors[] = 'Username harus diisi';
    } elseif (strlen($form_data['username']) < 3) {
        $errors[] = 'Username minimal 3 karakter';
    }
    
    // 2. Validasi Email
    if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format Email tidak valid';
    }
    
    // 3. Validasi Password
    $min_pass = defined('PASSWORD_MIN_LENGTH') ? PASSWORD_MIN_LENGTH : 6;
    if (empty($password)) {
        $errors[] = 'Password harus diisi';
    } elseif (strlen($password) < $min_pass) {
        $errors[] = 'Password minimal ' . $min_pass . ' karakter';
    }
    
    // 4. Konfirmasi Password
    if ($password !== $confirm_password) {
        $errors[] = 'Konfirmasi password tidak sesuai';
    }
    
    // 5. Validasi Nama Lengkap
    if (empty($form_data['nama_lengkap'])) {
        $errors[] = 'Nama lengkap harus diisi';
    }
    
    // 6. Validasi Syarat dan Ketentuan
    if (!$form_data['agree_terms']) {
        $errors[] = 'Anda harus menyetujui syarat dan ketentuan';
    }
    
    // --- Proses Pendaftaran Jika Tidak Ada Error Validasi Lokal ---
    if (empty($errors)) {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$form_data['username'], $form_data['email']]);
            
            if ($stmt->fetch()) {
                $errors[] = 'Username atau Email sudah terdaftar';
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Gunakan placeholder :field_name untuk kejelasan
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, password, email, nama_lengkap, nis, alamat, no_telepon, role) 
                    VALUES (:username, :password, :email, :nama_lengkap, :nis, :alamat, :no_telepon, 'siswa')
                ");
                
                $stmt->execute([
                    ':username' => $form_data['username'],
                    ':password' => $hashed_password,
                    ':email' => $form_data['email'],
                    ':nama_lengkap' => $form_data['nama_lengkap'],
                    ':nis' => $form_data['nis'] ?: null, // Simpan null jika kosong
                    ':alamat' => $form_data['alamat'] ?: null, 
                    ':no_telepon' => $form_data['no_telepon'] ?: null,
                ]);
                
                // Get the new user ID
                $user_id = $pdo->lastInsertId();
                
                // Log activity (Pastikan tabel activity_logs ada)
                if (defined('ENABLE_ACTIVITY_LOGS') && ENABLE_ACTIVITY_LOGS) {
                    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, 'REGISTER', 'User registrasi baru: ' . $form_data['username'], $_SERVER['REMOTE_ADDR']]);
                }
                
                // Redirect ke halaman login dengan pesan sukses
                $_SESSION['success_message'] = 'Pendaftaran berhasil! Silakan login.';
                header('Location: login.php');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = 'Terjadi kesalahan sistem database. Silakan coba lagi. ' . (DEBUG_MODE ? $e->getMessage() : '');
            error_log("REGISTER ERROR: " . $e->getMessage());
        }
    }
} else {
    // Jika bukan POST request, pastikan form_data bersih
    // Form data sudah bersih secara default
}

// Mengambil kembali data untuk 'sticky form'
$username_val = htmlspecialchars($form_data['username']);
$email_val = htmlspecialchars($form_data['email']);
$nama_lengkap_val = htmlspecialchars($form_data['nama_lengkap']);
$nis_val = htmlspecialchars($form_data['nis']);
$alamat_val = htmlspecialchars($form_data['alamat']);
$no_telepon_val = htmlspecialchars($form_data['no_telepon']);
$checked_terms = $form_data['agree_terms'] ? 'checked' : '';

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
                    <input id="username" name="username" type="text" required value="<?= $username_val ?>"
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Masukkan username">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input id="email" name="email" type="email" required value="<?= $email_val ?>"
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Masukkan email">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Minimal <?= defined('PASSWORD_MIN_LENGTH') ? PASSWORD_MIN_LENGTH : 6 ?> karakter">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password *</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Ulangi password">
                </div>
                
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                    <input id="nama_lengkap" name="nama_lengkap" type="text" required value="<?= $nama_lengkap_val ?>"
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Masukkan nama lengkap">
                </div>
                
                <div>
                    <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                    <input id="nis" name="nis" type="text" value="<?= $nis_val ?>"
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Nomor Induk Siswa">
                </div>
                
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"
                              class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Masukkan alamat lengkap"><?= $alamat_val ?></textarea>
                </div>
                
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                    <input id="no_telepon" name="no_telepon" type="tel" value="<?= $no_telepon_val ?>"
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Contoh: 081234567890">
                </div>
            </div>

            <div class="flex items-center">
                <input id="agree_terms" name="agree_terms" type="checkbox" required <?= $checked_terms ?>
                       class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                <label for="agree_terms" class="ml-2 block text-sm text-gray-900">
                    Saya menyetujui 
                    <a href="#" class="text-yellow-600 hover:text-yellow-500">syarat dan ketentuan</a> *
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
        // Password confirmation validation (Client-side)
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword && password !== confirmPassword) {
                confirmPasswordInput.classList.add('border-red-500');
                confirmPasswordInput.classList.remove('border-green-500');
                confirmPasswordInput.setCustomValidity('Password tidak cocok.');
            } else if (confirmPassword) {
                confirmPasswordInput.classList.remove('border-red-500');
                confirmPasswordInput.classList.add('border-green-500');
                confirmPasswordInput.setCustomValidity(''); // Hapus pesan error kustom
            } else {
                confirmPasswordInput.classList.remove('border-red-500', 'border-green-500');
                confirmPasswordInput.setCustomValidity('');
            }
        }

        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    </script>
</body>
</html>