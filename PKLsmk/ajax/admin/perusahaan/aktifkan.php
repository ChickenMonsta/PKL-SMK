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
    
    if ($id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID perusahaan tidak valid'
        ]);
        exit();
    }
    
    try {
        // Check if perusahaan exists
        $stmt = $pdo->prepare("SELECT nama_perusahaan FROM perusahaan WHERE id = ?");
        $stmt->execute([$id]);
        $perusahaan = $stmt->fetch();
        
        if (!$perusahaan) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Perusahaan tidak ditemukan'
            ]);
            exit();
        }
        
        // Update status to active
        $stmt = $pdo->prepare("UPDATE perusahaan SET status = 'active' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'AKTIFKAN_PERUSAHAAN', "Mengaktifkan perusahaan: {$perusahaan['nama_perusahaan']} (ID: $id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Perusahaan berhasil diaktifkan'
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