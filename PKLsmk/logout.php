<?php
session_start();

// Log activity if user is logged in
if (isset($_SESSION['user_id'])) {
    require_once 'includes/config.php';
    require_once 'includes/database.php';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], 'LOGOUT', 'User logout', $_SERVER['REMOTE_ADDR']]);
    } catch (PDOException $e) {
        // Continue with logout even if logging fails
    }
}

// Destroy session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>