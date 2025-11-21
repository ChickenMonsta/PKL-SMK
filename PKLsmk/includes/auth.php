<?php
/**
 * Authentication & Authorization Functions
 * Handles user login, logout, and permission checks
 */

// ==================== SESSION FUNCTIONS ====================

function isLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['username']) && 
           !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'nis' => $_SESSION['nis'] ?? null, // Ditambahkan untuk kelengkapan
        'foto_profil' => $_SESSION['foto_profil'] ?? null
    ];
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isSiswa() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'siswa';
}

function hasRole($role) {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// ==================== LOGIN FUNCTIONS ====================

function login($username, $password, $remember = false) {
    global $pdo;
    
    try {
        // Check login attempts
        if (isLoginLocked($username)) {
            return [
                'success' => false,
                'message' => 'Akun terkunci sementara. Coba lagi dalam ' . LOGIN_LOCKOUT_TIME / 60 . ' menit.'
            ];
        }
        
        // Find user
        $stmt = $pdo->prepare("
            SELECT * FROM users 
            WHERE (username = ? OR email = ?) AND status = 'active'
        ");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            recordLoginAttempt($username, false);
            return [
                'success' => false,
                'message' => 'Username atau password salah'
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            recordLoginAttempt($username, false);
            return [
                'success' => false,
                'message' => 'Username atau password salah'
            ];
        }
        
        // Login successful
        recordLoginAttempt($username, true);
        
        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['nis'] = $user['nis']; // <-- PERBAIKAN UTAMA: NIS disimpan
        $_SESSION['role'] = $user['role'];
        $_SESSION['foto_profil'] = $user['foto_profil'];
        $_SESSION['last_activity'] = time();
        
        // Update last login
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Log activity
        logActivity($user['id'], 'LOGIN', 'User berhasil login dari IP: ' . $_SERVER['REMOTE_ADDR']);
        
        // Handle remember me
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + REMEMBER_ME_DURATION;
            
            setcookie('remember_token', $token, $expires, '/', '', true, true);
            
            // Store token in database
            $stmt = $pdo->prepare("
                UPDATE users 
                SET remember_token = ?, remember_token_expires = ? 
                WHERE id = ?
            ");
            $stmt->execute([hash('sha256', $token), date('Y-m-d H:i:s', $expires), $user['id']]);
        }
        
        return [
            'success' => true,
            'message' => 'Login berhasil',
            'user' => $user
        ];
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan sistem'
        ];
    }
}

function logout() {
    if (isLoggedIn()) {
        // Log activity before destroying session
        logActivity($_SESSION['user_id'], 'LOGOUT', 'User logout');
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
            
            // Clear token from database
            global $pdo;
            try {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET remember_token = NULL, remember_token_expires = NULL 
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
            } catch (Exception $e) {
                error_log("Logout error: " . $e->getMessage());
            }
        }
    }
    
    // Destroy session
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

// ==================== LOGIN ATTEMPT TRACKING ====================

function recordLoginAttempt($username, $success) {
    if (!$success) {
        $key = 'login_attempts_' . md5($username . $_SERVER['REMOTE_ADDR']);
        $attempts = $_SESSION[$key] ?? 0;
        $_SESSION[$key] = $attempts + 1;
        $_SESSION[$key . '_time'] = time();
    } else {
        // Clear attempts on successful login
        $key = 'login_attempts_' . md5($username . $_SERVER['REMOTE_ADDR']);
        unset($_SESSION[$key]);
        unset($_SESSION[$key . '_time']);
    }
}

function isLoginLocked($username) {
    $key = 'login_attempts_' . md5($username . $_SERVER['REMOTE_ADDR']);
    $attempts = $_SESSION[$key] ?? 0;
    $lastAttempt = $_SESSION[$key . '_time'] ?? 0;
    
    if ($attempts >= LOGIN_MAX_ATTEMPTS) {
        if ((time() - $lastAttempt) < LOGIN_LOCKOUT_TIME) {
            return true;
        } else {
            // Reset after lockout period
            unset($_SESSION[$key]);
            unset($_SESSION[$key . '_time']);
        }
    }
    
    return false;
}

// ==================== REMEMBER ME FUNCTIONS ====================

function checkRememberMe() {
    if (isLoggedIn()) return;
    
    if (!isset($_COOKIE['remember_token'])) return;
    
    global $pdo;
    try {
        $token = $_COOKIE['remember_token'];
        $hashedToken = hash('sha256', $token);
        
        $stmt = $pdo->prepare("
            SELECT * FROM users 
            WHERE remember_token = ? 
            AND remember_token_expires > NOW() 
            AND status = 'active'
        ");
        $stmt->execute([$hashedToken]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Auto login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['nis'] = $user['nis']; // <-- PERBAIKAN UTAMA: NIS disimpan
            $_SESSION['role'] = $user['role'];
            $_SESSION['foto_profil'] = $user['foto_profil'];
            $_SESSION['last_activity'] = time();
            
            logActivity($user['id'], 'AUTO_LOGIN', 'User auto-login via remember me token');
        } else {
            // Invalid or expired token
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
    } catch (Exception $e) {
        error_log("Remember me error: " . $e->getMessage());
    }
}

// ==================== AUTHORIZATION FUNCTIONS ====================

function requireLogin() {
    if (!isLoggedIn()) {
        // Store intended URL
        $_SESSION['intended_url'] = currentUrl();
        
        // Check if AJAX request
        if (isAjaxRequest()) {
            jsonError('Sesi Anda telah berakhir. Silakan login kembali.', 401);
        }
        
        redirect('login.php?redirect=' . urlencode($_SESSION['intended_url']));
    }
}

function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        if (isAjaxRequest()) {
            jsonError('Anda tidak memiliki akses ke halaman ini', 403);
        }
        
        redirect('index.php?page=dashboard&error=forbidden');
    }
}

function requireSiswa() {
    requireLogin();
    
    if (!isSiswa()) {
        if (isAjaxRequest()) {
            jsonError('Anda tidak memiliki akses ke halaman ini', 403);
        }
        
        redirect('index.php?page=dashboard&error=forbidden');
    }
    
    // TAMBAHAN LOGIKA PENTING: Untuk memastikan data NIS ter-load saat requireSiswa dipanggil 
    // Walaupun sudah diperbaiki di login, ini bisa jadi fallback yang baik
    if (!isset($_SESSION['nis'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT nis FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $data = $stmt->fetch();
        if ($data) {
            $_SESSION['nis'] = $data['nis'];
        }
    }
}

function requireRole($role) {
    requireLogin();
    
    if (!hasRole($role)) {
        if (isAjaxRequest()) {
            jsonError('Anda tidak memiliki akses ke halaman ini', 403);
        }
        
        redirect('index.php?page=dashboard&error=forbidden');
    }
}

// ==================== HELPER FUNCTIONS ====================

function isAjaxRequest() {
    return (
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
         strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
        (isset($_SERVER['HTTP_ACCEPT']) && 
         strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    );
}

function getIntendedUrl() {
    $url = $_SESSION['intended_url'] ?? 'index.php?page=dashboard';
    unset($_SESSION['intended_url']);
    return $url;
}

// Check remember me on every page load
checkRememberMe();
?>