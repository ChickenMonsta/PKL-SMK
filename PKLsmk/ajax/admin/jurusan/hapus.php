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
    $action = $_POST['action'] ?? 'nonaktifkan'; // nonaktifkan or aktifkan
    
    if ($id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID jurusan tidak valid'
        ]);
        exit();
    }
    
    try {
        // Check if jurusan exists
        $stmt = $pdo->prepare("SELECT kode_jurusan, nama_jurusan FROM jurusan WHERE id = ?");
        $stmt->execute([$id]);
        $jurusan = $stmt->fetch();
        
        if (!$jurusan) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Jurusan tidak ditemukan'
            ]);
            exit();
        }
        
        // Check if jurusan has active pendaftaran
        if ($action == 'nonaktifkan') {
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pendaftaran_pkl WHERE jurusan_id = ? AND status IN ('pending', 'diterima')");
            $stmt->execute([$id]);
            $active_pendaftaran = $stmt->fetch()['total'];
            
            if ($active_pendaftaran > 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tidak dapat menonaktifkan jurusan yang masih memiliki pendaftaran aktif'
                ]);
                exit();
            }
        }
        
        // Update status
        $new_status = $action == 'nonaktifkan' ? 'inactive' : 'active';
        $stmt = $pdo->prepare("UPDATE jurusan SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        
        // Log activity
        $action_text = $action == 'nonaktifkan' ? 'Menonaktifkan' : 'Mengaktifkan';
        logActivity($_SESSION['user_id'], strtoupper($action) . '_JURUSAN', "$action_text jurusan: {$jurusan['nama_jurusan']} ({$jurusan['kode_jurusan']})");
        
        echo json_encode([
            'status' => 'success',
            'message' => "Jurusan berhasil di" . ($action == 'nonaktifkan' ? 'nonaktifkan' : 'aktifkan')
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