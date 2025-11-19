<?php
require_once 'bootstrap.php';

if (!isSiswa()) {
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

    $user_id = $_SESSION['user_id'];
    $jurusan_id = intval($_POST['jurusan_id'] ?? 0);
    $perusahaan_id = intval($_POST['perusahaan_id'] ?? 0);
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $alasan_pkl = sanitizeInput($_POST['alasan_pkl'] ?? '');

    // Validasi input
    $errors = [];

    // Cek apakah sudah ada pendaftaran aktif
    $stmt = $pdo->prepare("SELECT id FROM pendaftaran_pkl WHERE user_id = ? AND status IN ('pending', 'diterima')");
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Anda sudah memiliki pendaftaran yang sedang diproses'
        ]);
        exit();
    }

    if ($jurusan_id <= 0) {
        $errors[] = 'Pilih jurusan yang valid';
    }

    if ($perusahaan_id <= 0) {
        $errors[] = 'Pilih perusahaan yang valid';
    }

    if (empty($tanggal_mulai) || !validateDate($tanggal_mulai)) {
        $errors[] = 'Tanggal mulai tidak valid';
    }

    if (empty($tanggal_selesai) || !validateDate($tanggal_selesai)) {
        $errors[] = 'Tanggal selesai tidak valid';
    }

    if ($tanggal_mulai && $tanggal_selesai && $tanggal_selesai <= $tanggal_mulai) {
        $errors[] = 'Tanggal selesai harus setelah tanggal mulai';
    }

    if (empty($alasan_pkl) || strlen($alasan_pkl) < 10) {
        $errors[] = 'Alasan PKL minimal 10 karakter';
    }

    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'message' => implode(', ', $errors)
        ]);
        exit();
    }

    try {
        // Insert pendaftaran
        $stmt = $pdo->prepare("
            INSERT INTO pendaftaran_pkl 
            (user_id, jurusan_id, perusahaan_id, tanggal_mulai, tanggal_selesai, alasan_pkl, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        $stmt->execute([
            $user_id,
            $jurusan_id,
            $perusahaan_id,
            $tanggal_mulai,
            $tanggal_selesai,
            $alasan_pkl
        ]);

        // Log activity
        logActivity($user_id, 'PENDAFTARAN_PKL', "Mengajukan pendaftaran PKL");

        echo json_encode([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil dikirim! Status akan diperiksa oleh admin.'
        ]);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan database. Silakan coba lagi.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>