<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Token keamanan tidak valid'
        ]);
        exit();
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
        echo json_encode([
            'status' => 'error',
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    try {
        // Check if jurusan exists
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE id = ?");
        $stmt->execute([$id]);
        
        if (!$stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Jurusan tidak ditemukan'
            ]);
            exit();
        }
        
        // Check for duplicate kode jurusan (excluding current record)
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE kode_jurusan = ? AND id != ?");
        $stmt->execute([$kode_jurusan, $id]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Kode jurusan sudah digunakan'
            ]);
            exit();
        }
        
        // Check for duplicate nama jurusan (excluding current record)
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE nama_jurusan = ? AND id != ?");
        $stmt->execute([$nama_jurusan, $id]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Nama jurusan sudah terdaftar'
            ]);
            exit();
        }
        
        // Update jurusan
        $stmt = $pdo->prepare("UPDATE jurusan SET kode_jurusan = ?, nama_jurusan = ?, deskripsi = ?, kuota = ?, status = ? WHERE id = ?");
        $stmt->execute([$kode_jurusan, $nama_jurusan, $deskripsi, $kuota, $status, $id]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'EDIT_JURUSAN', "Mengedit jurusan: $nama_jurusan ($kode_jurusan)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Jurusan berhasil diperbarui'
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan database: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>