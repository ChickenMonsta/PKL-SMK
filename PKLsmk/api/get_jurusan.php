<?php
require_once '../bootstrap.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    sendJson(['error' => 'ID jurusan tidak valid'], 400);
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE id = ?");
    $stmt->execute([$id]);
    $jurusan = $stmt->fetch();

    if (!$jurusan) {
        sendJson(['error' => 'Jurusan tidak ditemukan'], 404);
    }

    sendJson($jurusan, 200);
    
} catch (PDOException $e) {
    sendJson(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
}
?>