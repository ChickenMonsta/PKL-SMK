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
    $status = $_POST['status'] ?? '';
    $catatan = sanitizeInput($_POST['catatan'] ?? '');
    
    // Validasi input
    $errors = [];
    
    if ($id <= 0) {
        $errors[] = 'ID pendaftaran tidak valid';
    }
    
    if (!in_array($status, ['diterima', 'ditolak'])) {
        $errors[] = 'Status tidak valid';
    }
    
    if (!empty($errors)) {
        sendJson(['status' => 'error', 'message' => implode(', ', $errors)], 400);
    }
    
    try {
        // Check if pendaftaran exists
        $stmt = $pdo->prepare("
            SELECT p.*, u.nama_lengkap, u.email, j.nama_jurusan, pr.nama_perusahaan 
            FROM pendaftaran_pkl p 
            JOIN users u ON p.user_id = u.id 
            JOIN jurusan j ON p.jurusan_id = j.id 
            JOIN perusahaan pr ON p.perusahaan_id = pr.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $pendaftaran = $stmt->fetch();
        
        if (!$pendaftaran) {
            sendJson(['status' => 'error', 'message' => 'Pendaftaran tidak ditemukan'], 404);
        }
        
        // Check if status is already set
        if ($pendaftaran['status'] == $status) {
            sendJson(['status' => 'error', 'message' => "Status pendaftaran sudah $status"], 400);
        }
        
        // Check kuota perusahaan if status is diterima
        if ($status == 'diterima') {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as total_diterima 
                FROM pendaftaran_pkl 
                WHERE perusahaan_id = ? AND status = 'diterima'
            ");
            $stmt->execute([$pendaftaran['perusahaan_id']]);
            $total_diterima = $stmt->fetch()['total_diterima'];
            
            $stmt = $pdo->prepare("SELECT kuota FROM perusahaan WHERE id = ?");
            $stmt->execute([$pendaftaran['perusahaan_id']]);
            $kuota_perusahaan = $stmt->fetch()['kuota'];
            
            if ($total_diterima >= $kuota_perusahaan) {
                sendJson(['status' => 'error', 'message' => 'Kuota perusahaan sudah penuh'], 409);
            }
        }
        
        // Update status pendaftaran
        $stmt = $pdo->prepare("UPDATE pendaftaran_pkl SET status = ?, catatan_admin = ? WHERE id = ?");
        $stmt->execute([$status, $catatan, $id]);
        
        // Log activity
        $status_text = $status == 'diterima' ? 'menerima' : 'menolak';
        logActivity($_SESSION['user_id'], 'UPDATE_STATUS_PENDAFTARAN', "$status_text pendaftaran dari {$pendaftaran['nama_lengkap']}");
        
        // Send notification email (you can implement this later)
        // sendStatusNotification($pendaftaran['email'], $pendaftaran['nama_lengkap'], $status, $catatan);
        
        sendJson(['status' => 'success', 'message' => "Pendaftaran berhasil di$status_text"], 200);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}

// Function to send notification email (placeholder)
function sendStatusNotification($email, $nama, $status, $catatan) {
    // Implement email notification here
    // You can use PHPMailer or other email libraries
    error_log("Would send email to: $email - Status: $status");
}
?>