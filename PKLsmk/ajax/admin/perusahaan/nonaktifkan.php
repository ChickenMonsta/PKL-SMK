<?php
require_once __DIR__ . '/../../../bootstrap.php';

if (!isAdmin()) {
    http_response_code(403);
    sendJson(['status' => 'error', 'message' => 'Unauthorized access'], 403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        sendJson(['status' => 'error', 'message' => 'Token keamanan tidak valid'], 400);
        exit();
    }

    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        sendJson(['status' => 'error', 'message' => 'ID perusahaan tidak valid'], 400);
    }
    
    try {
        // Check if perusahaan exists
        $stmt = $pdo->prepare("SELECT nama_perusahaan FROM perusahaan WHERE id = ?");
        $stmt->execute([$id]);
        $perusahaan = $stmt->fetch();
        
        if (!$perusahaan) {
            sendJson(['status' => 'error', 'message' => 'Perusahaan tidak ditemukan'], 404);
        }
        
        // Update status to inactive
        $stmt = $pdo->prepare("UPDATE perusahaan SET status = 'inactive' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'NONAKTIFKAN_PERUSAHAAN', "Menonaktifkan perusahaan: {$perusahaan['nama_perusahaan']} (ID: $id)");
        
        sendJson(['status' => 'success', 'message' => 'Perusahaan berhasil dinonaktifkan'], 200);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>