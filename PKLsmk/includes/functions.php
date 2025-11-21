<?php
/**
 * Helper Functions Library
 * Contains all utility functions used across the application
 */

// ==================== DATE & TIME FUNCTIONS ====================

function formatDate($date, $format = DATE_FORMAT) {
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    try {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date($format, $timestamp);
    } catch (Exception $e) {
        return '-';
    }
}

function formatDateTime($datetime, $format = DATETIME_FORMAT) {
    return formatDate($datetime, $format);
}

function getTimeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
    if ($diff < 604800) return floor($diff / 86400) . ' hari yang lalu';
    
    return formatDate($datetime);
}

// ==================== VALIDATION FUNCTIONS ====================

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    // Indonesian phone number format
    return preg_match('/^(\+62|62|0)[0-9]{9,12}$/', $phone);
}

function validatePassword($password) {
    return strlen($password) >= PASSWORD_MIN_LENGTH;
}

function validateUsername($username) {
    // Alphanumeric and underscore only, 3-20 characters
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

// ==================== SECURITY FUNCTIONS ====================

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function escapeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!ENABLE_CSRF_PROTECTION) return true;
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// ==================== FILE HANDLING FUNCTIONS ====================

function uploadFile($file, $allowedTypes = null, $customPath = null) {
    if (!ENABLE_FILE_UPLOAD) {
        throw new Exception('File upload is disabled');
    }
    
    $allowedTypes = $allowedTypes ?? ALLOWED_EXTENSIONS;
    $uploadDir = $customPath ?? UPLOAD_PATH;
    
    // Create upload directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error uploading file: ' . $file['error']);
    }
    
    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = time() . '_' . generateRandomString(8) . '.' . $extension;
    $targetPath = $uploadDir . $fileName;
    
    // Validate file type
    if (!in_array($extension, $allowedTypes)) {
        throw new Exception('File type not allowed. Allowed: ' . implode(', ', $allowedTypes));
    }
    
    // Validate file size
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('File size exceeds maximum allowed: ' . formatBytes(MAX_FILE_SIZE));
    }
    
    // Additional security: check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ];
    
    if (isset($allowedMimes[$extension]) && $mimeType !== $allowedMimes[$extension]) {
        throw new Exception('Invalid file type detected');
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    } else {
        throw new Exception('Failed to move uploaded file');
    }
}

function deleteFile($filename, $directory = null) {
    $directory = $directory ?? UPLOAD_PATH;
    $filePath = $directory . $filename;
    
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// ==================== DATABASE FUNCTIONS ====================

function logActivity($user_id, $action, $description, $pdo = null) {
    if (!ENABLE_ACTIVITY_LOGS) return false;
    
    global $pdo;
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
        return false;
    }
}

function getPaginationData($total, $page = 1, $perPage = ITEMS_PER_PAGE) {
    $totalPages = ceil($total / $perPage);
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $page > 1,
        'has_next' => $page < $totalPages
    ];
}

function getSetting($key, $default = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT value FROM pengaturan WHERE key_name = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function setSetting($key, $value, $description = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO pengaturan (key_name, value, description) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE value = ?, description = ?
        ");
        return $stmt->execute([$key, $value, $description, $value, $description]);
    } catch (Exception $e) {
        error_log("Settings error: " . $e->getMessage());
        return false;
    }
}

// ==================== UI HELPER FUNCTIONS ====================

function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        'diterima' => 'bg-green-100 text-green-800 border border-green-200',
        'approved' => 'bg-green-100 text-green-800 border border-green-200',
        'ditolak' => 'bg-red-100 text-red-800 border border-red-200',
        'rejected' => 'bg-red-100 text-red-800 border border-red-200',
        'selesai' => 'bg-blue-100 text-blue-800 border border-blue-200',
        'completed' => 'bg-blue-100 text-blue-800 border border-blue-200',
        'active' => 'bg-green-100 text-green-800 border border-green-200',
        'inactive' => 'bg-gray-100 text-gray-800 border border-gray-200'
    ];
    
    $statusText = ucfirst(str_replace('_', ' ', $status));
    $badgeClass = $badges[strtolower($status)] ?? 'bg-gray-100 text-gray-800 border border-gray-200';
    
    return "<span class='px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center {$badgeClass}'>{$statusText}</span>";
}

function alert($message, $type = 'info', $dismissible = true) {
    $icons = [
        'success' => 'fa-check-circle',
        'error' => 'fa-times-circle',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle'
    ];
    
    $colors = [
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800'
    ];
    
    $icon = $icons[$type] ?? $icons['info'];
    $color = $colors[$type] ?? $colors['info'];
    $dismiss = $dismissible ? '<button type="button" class="close-alert ml-auto"><i class="fas fa-times"></i></button>' : '';
    
    return "
    <div class='alert {$color} border rounded-lg p-4 mb-4 flex items-center' role='alert'>
        <i class='fas {$icon} mr-3 text-lg'></i>
        <span class='flex-1'>{$message}</span>
        {$dismiss}
    </div>";
}

// ==================== JSON RESPONSE FUNCTIONS ====================

function sendJson($data, $httpCode = 200) {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($httpCode);
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function jsonSuccess($message, $data = []) {
    sendJson([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ]);
}

function jsonError($message, $httpCode = 400, $errors = []) {
    sendJson([
        'status' => 'error',
        'message' => $message,
        'errors' => $errors
    ], $httpCode);
}

// ==================== NOTIFICATION FUNCTIONS ====================

function createNotification($user_id, $title, $message, $type = 'info', $link = null) {
    if (!ENABLE_NOTIFICATIONS) return false;
    
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type, link) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$user_id, $title, $message, $type, $link]);
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

function getUnreadNotifications($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// ==================== URL & REDIRECT FUNCTIONS ====================

function redirect($url, $permanent = false) {
    if (!headers_sent()) {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    } else {
        echo "<script>window.location.href='{$url}';</script>";
        exit();
    }
}

function currentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function baseUrl($path = '') {
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}