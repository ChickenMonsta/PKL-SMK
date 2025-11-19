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
        echo json_encode([
            'status' => 'error',
            'message' => implode(', ', $errors)
        ]);
        exit();
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
            echo json_encode([
                'status' => 'error',
                'message' => 'Pendaftaran tidak ditemukan'
            ]);
            exit();
        }
        
        // Check if status is already set
        if ($pendaftaran['status'] == $status) {
            echo json_encode([
                'status' => 'error',
                'message' => "Status pendaftaran sudah $status"
            ]);
            exit();
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
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kuota perusahaan sudah penuh'
                ]);
                exit();
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
        
        echo json_encode([
            'status' => 'success',
            'message' => "Pendaftaran berhasil di$status_text"
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

// Function to send notification email (placeholder)
function sendStatusNotification($email, $nama, $status, $catatan) {
    // Implement email notification here
    // You can use PHPMailer or other email libraries
    error_log("Would send email to: $email - Status: $status");
}
?>