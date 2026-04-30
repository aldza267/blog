<?php
// hapus_penulis.php - Menghapus data penulis
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

// Cek apakah penulis memiliki artikel — jika ada, batalkan penghapusan
$stmtCekArtikel = $conn->prepare("SELECT COUNT(*) AS total FROM artikel WHERE id_penulis = ?");
$stmtCekArtikel->bind_param('i', $id);
$stmtCekArtikel->execute();
$hasilCek = $stmtCekArtikel->get_result()->fetch_assoc();
$stmtCekArtikel->close();

if ($hasilCek['total'] > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Penulis tidak dapat dihapus karena masih memiliki ' . $hasilCek['total'] . ' artikel.'
    ]);
    exit;
}

// Ambil nama foto sebelum dihapus
$stmtFoto = $conn->prepare("SELECT foto FROM penulis WHERE id = ?");
$stmtFoto->bind_param('i', $id);
$stmtFoto->execute();
$hasilFoto = $stmtFoto->get_result();

if ($hasilFoto->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Penulis tidak ditemukan.']);
    exit;
}

$dataFoto = $hasilFoto->fetch_assoc();
$stmtFoto->close();

// Hapus data dari database
$stmtHapus = $conn->prepare("DELETE FROM penulis WHERE id = ?");
$stmtHapus->bind_param('i', $id);

if ($stmtHapus->execute()) {
    // Hapus file foto fisik jika bukan default.png
    $foto      = $dataFoto['foto'];
    $uploadDir = __DIR__ . '/uploads_penulis/';
    if ($foto !== 'default.png' && file_exists($uploadDir . $foto)) {
        @unlink($uploadDir . $foto);
    }

    echo json_encode(['status' => 'success', 'message' => 'Penulis berhasil dihapus.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $conn->error]);
}

$stmtHapus->close();
$conn->close();
