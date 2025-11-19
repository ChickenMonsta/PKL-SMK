<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID jurusan tidak valid']);
    exit();
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE id = ?");
    $stmt->execute([$id]);
    $jurusan = $stmt->fetch();

    if (!$jurusan) {
        echo json_encode(['error' => 'Jurusan tidak ditemukan']);
        exit();
    }

    echo json_encode($jurusan);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>