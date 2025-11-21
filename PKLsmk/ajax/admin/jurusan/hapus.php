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
    $action = $_POST['action'] ?? 'nonaktifkan'; // nonaktifkan or aktifkan
    
    if ($id <= 0) {
        sendJson(['status' => 'error', 'message' => 'ID jurusan tidak valid'], 400);
    }
    
    try {
        // Check if jurusan exists
        $stmt = $pdo->prepare("SELECT kode_jurusan, nama_jurusan FROM jurusan WHERE id = ?");
        $stmt->execute([$id]);
        $jurusan = $stmt->fetch();
        
        if (!$jurusan) {
            sendJson(['status' => 'error', 'message' => 'Jurusan tidak ditemukan'], 404);
        }
        
        // Check if jurusan has active pendaftaran
        if ($action == 'nonaktifkan') {
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pendaftaran_pkl WHERE jurusan_id = ? AND status IN ('pending', 'diterima')");
            $stmt->execute([$id]);
            $active_pendaftaran = $stmt->fetch()['total'];
            
            if ($active_pendaftaran > 0) {
                sendJson(['status' => 'error', 'message' => 'Tidak dapat menonaktifkan jurusan yang masih memiliki pendaftaran aktif'], 409);
            }
        }
        
        // Update status
        $new_status = $action == 'nonaktifkan' ? 'inactive' : 'active';
        $stmt = $pdo->prepare("UPDATE jurusan SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        
        // Log activity
        $action_text = $action == 'nonaktifkan' ? 'Menonaktifkan' : 'Mengaktifkan';
        logActivity($_SESSION['user_id'], strtoupper($action) . '_JURUSAN', "$action_text jurusan: {$jurusan['nama_jurusan']} ({$jurusan['kode_jurusan']})");
        
        sendJson(['status' => 'success', 'message' => "Jurusan berhasil di" . ($action == 'nonaktifkan' ? 'nonaktifkan' : 'aktifkan')], 200);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>