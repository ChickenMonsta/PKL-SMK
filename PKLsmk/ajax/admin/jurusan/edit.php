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
    $kode_jurusan = strtoupper(sanitizeInput($_POST['kode_jurusan'] ?? ''));
    $nama_jurusan = sanitizeInput($_POST['nama_jurusan'] ?? '');
    $deskripsi = sanitizeInput($_POST['deskripsi'] ?? '');
    $kuota = intval($_POST['kuota'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    // Validasi input
    $errors = [];
    
    if ($id <= 0) {
        $errors[] = 'ID jurusan tidak valid';
    }
    
    if (empty($kode_jurusan)) {
        $errors[] = 'Kode jurusan harus diisi';
    }
    
    if (strlen($kode_jurusan) > 10) {
        $errors[] = 'Kode jurusan maksimal 10 karakter';
    }
    
    if (empty($nama_jurusan)) {
        $errors[] = 'Nama jurusan harus diisi';
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
        // Check if jurusan exists
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE id = ?");
        $stmt->execute([$id]);
        
        if (!$stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Jurusan tidak ditemukan'], 404);
        }
        
        // Check for duplicate kode jurusan (excluding current record)
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE kode_jurusan = ? AND id != ?");
        $stmt->execute([$kode_jurusan, $id]);
        
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Kode jurusan sudah digunakan'], 409);
        }
        
        // Check for duplicate nama jurusan (excluding current record)
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE nama_jurusan = ? AND id != ?");
        $stmt->execute([$nama_jurusan, $id]);
        
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Nama jurusan sudah terdaftar'], 409);
        }
        
        // Update jurusan
        $stmt = $pdo->prepare("UPDATE jurusan SET kode_jurusan = ?, nama_jurusan = ?, deskripsi = ?, kuota = ?, status = ? WHERE id = ?");
        $stmt->execute([$kode_jurusan, $nama_jurusan, $deskripsi, $kuota, $status, $id]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'EDIT_JURUSAN', "Mengedit jurusan: $nama_jurusan ($kode_jurusan)");
        
        sendJson(['status' => 'success', 'message' => 'Jurusan berhasil diperbarui'], 200);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>