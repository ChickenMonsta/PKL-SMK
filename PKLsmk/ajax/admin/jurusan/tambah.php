<?php
require_once __DIR__ . '/../../../bootstrap.php';
requireAdmin();

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
        echo json_encode([
            'status' => 'error',
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    try {
        // Check if kode jurusan already exists
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE kode_jurusan = ?");
        $stmt->execute([$kode_jurusan]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Kode jurusan sudah digunakan'
            ]);
            exit();
        }
        
        // Check if nama jurusan already exists
        $stmt = $pdo->prepare("SELECT id FROM jurusan WHERE nama_jurusan = ?");
        $stmt->execute([$nama_jurusan]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Nama jurusan sudah terdaftar'
            ]);
            exit();
        }
        
        // Insert new jurusan
        $stmt = $pdo->prepare("INSERT INTO jurusan (kode_jurusan, nama_jurusan, deskripsi, kuota) VALUES (?, ?, ?, ?)");
        $stmt->execute([$kode_jurusan, $nama_jurusan, $deskripsi, $kuota]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'TAMBAH_JURUSAN', "Menambah jurusan: $nama_jurusan ($kode_jurusan)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Jurusan berhasil ditambahkan'
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