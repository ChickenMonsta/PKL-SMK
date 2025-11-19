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
        echo json_encode([
            'status' => 'error',
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    try {
        // Check if perusahaan already exists
        $stmt = $pdo->prepare("SELECT id FROM perusahaan WHERE nama_perusahaan = ?");
        $stmt->execute([$nama_perusahaan]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Perusahaan dengan nama tersebut sudah terdaftar'
            ]);
            exit();
        }
        
        // Insert new perusahaan
        $stmt = $pdo->prepare("INSERT INTO perusahaan (nama_perusahaan, alamat, kontak, kuota) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama_perusahaan, $alamat, $kontak, $kuota]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'TAMBAH_PERUSAHAAN', "Menambah perusahaan: $nama_perusahaan");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Perusahaan berhasil ditambahkan'
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