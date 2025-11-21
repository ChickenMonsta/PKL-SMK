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
        
        // Update status to active
        $stmt = $pdo->prepare("UPDATE perusahaan SET status = 'active' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Log activity
        logActivity($_SESSION['user_id'], 'AKTIFKAN_PERUSAHAAN', "Mengaktifkan perusahaan: {$perusahaan['nama_perusahaan']} (ID: $id)");

        sendJson(['status' => 'success', 'message' => 'Perusahaan diaktifkan'], 200);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
            sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
        sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>