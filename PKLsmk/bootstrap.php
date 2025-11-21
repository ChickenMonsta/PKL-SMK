<?php
/**
 * Bootstrap File - Application Initialization
 * Loaded by all pages to ensure consistent configuration
 */

// Prevent direct access
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    
    session_start();
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Define absolute path to root directory
define('ROOT_PATH', dirname(__FILE__));

// Include required files in correct order
$requiredFiles = [
    ROOT_PATH . '/includes/config.php',
    ROOT_PATH . '/includes/database.php',
    ROOT_PATH . '/includes/functions.php',
    ROOT_PATH . '/includes/auth.php'
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        die("Critical file missing: " . basename($file));
    }
    require_once $file;
}

// Set error handler for production
if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        error_log("Error [$errno]: $errstr in $errfile on line $errline");
        if (ini_get('display_errors')) {
            echo "An error occurred. Please try again later.";
        }
        return true;
    });
}

// Check database connection
if (!isset($pdo)) {
    die("Database connection failed. Please check your configuration.");
}

// Initialize CSRF token for forms
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Auto-logout inactive users (30 minutes)
if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
    $inactive_time = 1800; // 30 minutes
    if ((time() - $_SESSION['last_activity']) > $inactive_time) {
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit();
    }
}
$_SESSION['last_activity'] = time();

// Helper function to check maintenance mode
function isMaintenanceMode() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT value FROM pengaturan WHERE key_name = 'maintenance_mode'");
        $result = $stmt->fetch();
        return $result && $result['value'] == '1';
    } catch (Exception $e) {
        return false;
    }
}

// Check maintenance mode (except for admin)
if (isMaintenanceMode() && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    if (basename($_SERVER['PHP_SELF']) !== 'maintenance.php') {
        header('Location: maintenance.php');
        exit();
    }
}