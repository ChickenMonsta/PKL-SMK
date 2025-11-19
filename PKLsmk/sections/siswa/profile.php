<?php
require_once __DIR__ . '/../../bootstrap.php';
requireSiswa();

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Update profile jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = sanitizeInput($_POST['nama_lengkap']);
    $email = sanitizeInput($_POST['email']);
    $no_telepon = sanitizeInput($_POST['no_telepon']);
    $alamat = sanitizeInput($_POST['alamat']);

    try {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, email = ?, no_telepon = ?, alamat = ? WHERE id = ?");
        $stmt->execute([$nama_lengkap, $email, $no_telepon, $alamat, $user_id]);
        
        // Update session
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['email'] = $email;
        
        $success = "Profile berhasil diperbarui!";
    } catch (Exception $e) {
        $error = "Gagal memperbarui profile: " . $e->getMessage();
    }
}
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Profile Saya</h1>
            <p class="text-gray-600">Kelola informasi profile Anda</p>
        </div>

        <!-- Profile Form -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <?php if (isset($success)): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-800"><?= $success ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="text-red-800"><?= $error ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" disabled>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIS</label>
                        <input type="text" value="<?= htmlspecialchars($user['nis']) ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" disabled>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                        <input type="tel" name="no_telepon" value="<?= htmlspecialchars($user['no_telepon']) ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea name="alamat" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"><?= htmlspecialchars($user['alamat']) ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="index.php?page=siswa" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>