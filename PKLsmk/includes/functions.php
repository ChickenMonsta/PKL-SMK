<?php
require_once 'database.php';

function formatDate($date, $format = 'd M Y') {
    if (empty($date) || $date == '0000-00-00') {
        return '-';
    }
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd M Y H:i') {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($datetime));
}

function getStatusBadge($status) {
    $badges = [
        'active' => '<span class="badge bg-success">Aktif</span>',
        'inactive' => '<span class="badge bg-danger">Nonaktif</span>',
        'pending' => '<span class="badge bg-warning">Menunggu</span>',
        'diterima' => '<span class="badge bg-success">Diterima</span>',
        'ditolak' => '<span class="badge bg-danger">Ditolak</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

function uploadFile($file, $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error uploading file: ' . $file['error']);
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('File size exceeds maximum limit of 5MB');
    }

    // Check file type
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        throw new Exception('File type not allowed. Allowed types: ' . implode(', ', $allowedTypes));
    }

    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $fileExtension;
    $uploadPath = UPLOAD_PATH . $filename;

    // Create uploads directory if not exists
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }

    return $filename;
}

function deleteFile($filename) {
    if (!empty($filename) && file_exists(UPLOAD_PATH . $filename)) {
        return unlink(UPLOAD_PATH . $filename);
    }
    return false;
}

function getDashboardStats() {
    global $pdo;
    
    $stats = [];
    
    try {
        // Total siswa
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'siswa'");
        $stats['total_siswa'] = $stmt->fetch()['total'];
        
        // Total perusahaan
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM perusahaan WHERE status = 'active'");
        $stats['total_perusahaan'] = $stmt->fetch()['total'];
        
        // Total pendaftaran
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_pkl");
        $stats['total_pendaftaran'] = $stmt->fetch()['total'];
        
        // Pendaftaran pending
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_pkl WHERE status = 'pending'");
        $stats['pending_pendaftaran'] = $stmt->fetch()['total'];
    } catch (PDOException $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        // Return default values
        return [
            'total_siswa' => 0,
            'total_perusahaan' => 0,
            'total_pendaftaran' => 0,
            'pending_pendaftaran' => 0
        ];
    }
    
    return $stats;
}

function logActivity($userId, $action, $description = '') {
    global $pdo;
    
    try {
        // Buat tabel activity_logs jika belum ada
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                action VARCHAR(100) NOT NULL,
                description TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    } catch (PDOException $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

// Helper function untuk debug
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
?>