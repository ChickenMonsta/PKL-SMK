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
        echo json_encode([
            'status' => 'error',
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    try {
        // Check if perusahaan exists
        $stmt = $pdo->prepare("SELECT id FROM perusahaan WHERE id = ?");
        $stmt->execute([$id]);
        
        if (!$stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Perusahaan tidak ditemukan'
            ]);
            exit();
        }
        
        // Check for duplicate name (excluding current record)
        $stmt = $pdo->prepare("SELECT id FROM perusahaan WHERE nama_perusahaan = ? AND id != ?");
        $stmt->execute([$nama_perusahaan, $id]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Perusahaan dengan nama tersebut sudah terdaftar'
            ]);
            exit();
        }
        
        // Update perusahaan
        $stmt = $pdo->prepare("UPDATE perusahaan SET nama_perusahaan = ?, alamat = ?, kontak = ?, kuota = ?, status = ? WHERE id = ?");
        $stmt->execute([$nama_perusahaan, $alamat, $kontak, $kuota, $status, $id]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'EDIT_PERUSAHAAN', "Mengedit perusahaan: $nama_perusahaan (ID: $id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Perusahaan berhasil diperbarui'
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