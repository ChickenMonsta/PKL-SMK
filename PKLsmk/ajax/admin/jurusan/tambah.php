<?php
require_once __DIR__ . '/../../../bootstrap.php';
requireAdmin();

if (!isAdmin()) {
    sendJson(['status' => 'error', 'message' => 'Unauthorized access'], 403);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        sendJson(['status' => 'error', 'message' => 'Token keamanan tidak valid'], 400);
    }

    $kode_jurusan = strtoupper(sanitizeInput($_POST['kode_jurusan'] ?? ''));
    $nama_jurusan = sanitizeInput($_POST['nama_jurusan'] ?? '');
    $deskripsi = sanitizeInput($_POST['deskripsi'] ?? '');
    $kuota = intval($_POST['kuota'] ?? 0);
    
    // Validasi input
    $errors = [];
    
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
    
    if (!empty($errors)) {
        sendJson(['status' => 'error', 'message' => implode(', ', $errors)], 400);
    }
    
    try {
        // Check if kode jurusan already exists
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE kode_jurusan = ?");
        $stmt->execute([$kode_jurusan]);
        
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Kode jurusan sudah digunakan'], 409);
        }
        
        // Check if nama jurusan already exists
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE nama_jurusan = ?");
        $stmt->execute([$nama_jurusan]);
        
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Nama jurusan sudah terdaftar'], 409);
        }
        
        // Insert new jurusan
        $stmt = $pdo->prepare("INSERT INTO jurusan (kode_jurusan, nama_jurusan, deskripsi, kuota) VALUES (?, ?, ?, ?)");
        $stmt->execute([$kode_jurusan, $nama_jurusan, $deskripsi, $kuota]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'TAMBAH_JURUSAN', "Menambah jurusan: $nama_jurusan ($kode_jurusan)");
        
        sendJson(['status' => 'success', 'message' => 'Jurusan berhasil ditambahkan'], 201);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>