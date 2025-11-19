<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'pkl_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Konfigurasi Aplikasi
define('APP_NAME', 'Sistem PKL SMK Negeri 7');
define('APP_VERSION', '1.0.0');
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>