<?php
// Define absolute path to root directory
define('ROOT_PATH', dirname(__FILE__));

// Include all required files
require_once ROOT_PATH . '/includes/config.php';
require_once ROOT_PATH . '/includes/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';

echo "<!-- Bootstrap loaded successfully from: " . ROOT_PATH . " -->";
?>