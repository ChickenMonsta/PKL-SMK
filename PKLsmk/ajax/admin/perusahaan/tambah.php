<?php
require_once __DIR__ . '/../../../bootstrap.php';

    if (!isAdmin()) {
    sendJson(['status' => 'error', 'message' => 'Unauthorized access'], 403);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        sendJson(['status' => 'error', 'message' => 'Token keamanan tidak valid'], 400);
    }

    $nama_perusahaan = sanitizeInput($_POST['nama_perusahaan'] ?? '');
    $alamat = sanitizeInput($_POST['alamat'] ?? '');
    $kontak = sanitizeInput($_POST['kontak'] ?? '');
    $kuota = intval($_POST['kuota'] ?? 0);
    
    // Validasi input
    $errors = [];
    
    if (empty($nama_perusahaan)) {
        $errors[] = 'Nama perusahaan harus diisi';
    }
    
    if (empty($alamat)) {
        $errors[] = 'Alamat harus diisi';
    }
    
    if (empty($kontak)) {
        $errors[] = 'Kontak harus diisi';
    }
    
    if ($kuota <= 0) {
        $errors[] = 'Kuota harus lebih dari 0';
    }
    
    if (!empty($errors)) {
        sendJson(['status' => 'error', 'message' => implode(', ', $errors)], 400);
    }
    
    try {
        // Check if perusahaan already exists
        $stmt = $pdo->prepare("SELECT id FROM perusahaan WHERE nama_perusahaan = ?");
        $stmt->execute([$nama_perusahaan]);
        
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Perusahaan dengan nama tersebut sudah terdaftar'], 409);
        }
        
        // Insert new perusahaan
        $stmt = $pdo->prepare("INSERT INTO perusahaan (nama_perusahaan, alamat, kontak, kuota) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama_perusahaan, $alamat, $kontak, $kuota]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'TAMBAH_PERUSAHAAN', "Menambah perusahaan: $nama_perusahaan");
        
        sendJson(['status' => 'success', 'message' => 'Perusahaan berhasil ditambahkan'], 201);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>