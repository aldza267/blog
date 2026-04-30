<?php
// hapus_artikel.php - Menghapus data artikel beserta file gambar fisiknya
header('Content-Type: application/json');
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    exit;
}

// Ambil nama gambar sebelum dihapus
$stmtGambar = $conn->prepare("SELECT gambar FROM artikel WHERE id = ?");
$stmtGambar->bind_param('i', $id);
$stmtGambar->execute();
$hasilGambar = $stmtGambar->get_result();

if ($hasilGambar->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
    exit;
}

$dataGambar = $hasilGambar->fetch_assoc();
$stmtGambar->close();

// Hapus dari database
$stmtHapus = $conn->prepare("DELETE FROM artikel WHERE id = ?");
$stmtHapus->bind_param('i', $id);

if ($stmtHapus->execute()) {
    // Hapus file gambar fisik menggunakan unlink()
    $gambar    = $dataGambar['gambar'];
    $uploadDir = __DIR__ . '/uploads_artikel/';
    $filePath  = $uploadDir . $gambar;

    if (!empty($gambar) && file_exists($filePath)) {
        @unlink($filePath);
    }

    echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil dihapus.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus artikel: ' . $conn->error]);
}

$stmtHapus->close();
$conn->close();
