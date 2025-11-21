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

    $id = intval($_POST['id'] ?? 0);
    $nama_perusahaan = sanitizeInput($_POST['nama_perusahaan'] ?? '');
    $alamat = sanitizeInput($_POST['alamat'] ?? '');
    $kontak = sanitizeInput($_POST['kontak'] ?? '');
    $kuota = intval($_POST['kuota'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    // Validasi input
    $errors = [];
    
    if ($id <= 0) {
        $errors[] = 'ID perusahaan tidak valid';
    }
    
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
    
    if (!in_array($status, ['active', 'inactive'])) {
        $errors[] = 'Status tidak valid';
    }
    
    if (!empty($errors)) {
        sendJson(['status' => 'error', 'message' => implode(', ', $errors)], 400);
    }
    
    try {
        // Check if perusahaan exists
        $stmt = $pdo->prepare("SELECT id FROM perusahaan WHERE id = ?");
        $stmt->execute([$id]);
        
        if (!$stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Perusahaan tidak ditemukan'], 404);
        }
        
        // Check for duplicate name (excluding current record)
        $stmt = $pdo->prepare("SELECT id FROM perusahaan WHERE nama_perusahaan = ? AND id != ?");
        $stmt->execute([$nama_perusahaan, $id]);
        
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Perusahaan dengan nama tersebut sudah terdaftar'], 409);
        }
        
        // Update perusahaan
        $stmt = $pdo->prepare("UPDATE perusahaan SET nama_perusahaan = ?, alamat = ?, kontak = ?, kuota = ?, status = ? WHERE id = ?");
        $stmt->execute([$nama_perusahaan, $alamat, $kontak, $kuota, $status, $id]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'EDIT_PERUSAHAAN', "Mengedit perusahaan: $nama_perusahaan (ID: $id)");
        
        sendJson(['status' => 'success', 'message' => 'Perusahaan berhasil diperbarui'], 200);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>