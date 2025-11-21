<?php
require_once 'bootstrap.php';

if (!isSiswa()) {
    sendJson(['status' => 'error', 'message' => 'Unauthorized access'], 403);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        sendJson(['status' => 'error', 'message' => 'Token keamanan tidak valid'], 400);
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
        sendJson(['status' => 'error', 'message' => 'Anda sudah memiliki pendaftaran yang sedang diproses'], 409);
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
        sendJson(['status' => 'error', 'message' => implode(', ', $errors)], 400);
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

        sendJson(['status' => 'success', 'message' => 'Pendaftaran berhasil dikirim! Status akan diperiksa oleh admin.'], 201);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        sendJson(['status' => 'error', 'message' => 'Terjadi kesalahan database. Silakan coba lagi.'], 500);
    }
} else {
    sendJson(['status' => 'error', 'message' => 'Method not allowed'], 405);
}
?>