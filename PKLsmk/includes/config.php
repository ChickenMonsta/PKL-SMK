<?php
/**
 * Application Configuration
 * All settings should be defined here
 */

// Environment Detection
define('ENVIRONMENT', getenv('APP_ENV') ?: 'development'); // development, production
define('DEBUG_MODE', ENVIRONMENT === 'development');

// ********** PERBAIKAN UNTUK MENGHINDARI FATAL ERROR DAN WARNING **********
// Hanya definisikan ROOT_PATH jika belum terdefinisi.
// dirname(__DIR__) digunakan karena config.php berada di dalam folder '/includes/'.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__)); 
}
// ************************************************************************

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'pkl_system');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'Sistem PKL SMK Negeri 7 Batam');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/PKLsmk');

// Path Configuration
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');
define('INCLUDES_PATH', ROOT_PATH . '/includes/');

// File Upload Settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Session Settings
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('REMEMBER_ME_DURATION', 604800); // 7 days

// Pagination Settings
define('ITEMS_PER_PAGE', 10);
define('MAX_PAGINATION_LINKS', 5);

// Security Settings
define('ENABLE_CSRF_PROTECTION', true);
define('PASSWORD_MIN_LENGTH', 6);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Email Settings (for future implementation)
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_FROM', getenv('SMTP_FROM') ?: 'noreply@smkn7batam.sch.id');
define('SMTP_FROM_NAME', 'Sistem PKL SMKN 7');

// Date & Time Settings
date_default_timezone_set('Asia/Jakarta');
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

// Create necessary directories if they don't exist
$directories = [
    UPLOAD_PATH,
    ROOT_PATH . '/logs',
    ROOT_PATH . '/cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        // Suppress warning if directory creation fails (e.g., permission issue)
        @mkdir($dir, 0755, true); 
    }
}

// Application Status Messages
define('MSG_SUCCESS', 'success');
define('MSG_ERROR', 'error');
define('MSG_WARNING', 'warning');
define('MSG_INFO', 'info');

// API Settings (untuk pengembangan future)
define('API_ENABLED', false);
define('API_KEY', getenv('API_KEY') ?: '');

// Feature Flags
define('ENABLE_NOTIFICATIONS', true);
define('ENABLE_EMAIL_NOTIFICATIONS', false);
define('ENABLE_FILE_UPLOAD', true);
define('ENABLE_ACTIVITY_LOGS', true);