<?php
// update_artikel.php - Memperbarui data artikel
header('Content-Type: application/json');
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$id          = intval($_POST['id'] ?? 0);
$judul       = trim($_POST['judul'] ?? '');
$isi         = trim($_POST['isi'] ?? '');
$id_penulis  = intval($_POST['id_penulis'] ?? 0);
$id_kategori = intval($_POST['id_kategori'] ?? 0);

if ($id <= 0 || empty($judul) || empty($isi) || $id_penulis <= 0 || $id_kategori <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
    exit;
}

// Ambil data lama
$stmtCek = $conn->prepare("SELECT gambar FROM artikel WHERE id = ?");
$stmtCek->bind_param('i', $id);
$stmtCek->execute();
$hasilCek = $stmtCek->get_result();

if ($hasilCek->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
    exit;
}

$dataLama  = $hasilCek->fetch_assoc();
$gambarLama = $dataLama['gambar'];
$stmtCek->close();

$gambar    = $gambarLama;
$uploadDir = __DIR__ . '/uploads_artikel/';

// Proses upload gambar baru jika ada
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file    = $_FILES['gambar'];
    $maxSize = 2 * 1024 * 1024;

    if ($file['size'] > $maxSize) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran gambar melebihi 2 MB.']);
        exit;
    }

    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe file tidak diizinkan. Hanya JPEG, PNG, GIF, WEBP.']);
        exit;
    }

    $ekstensi = pathinfo($file['name'], PATHINFO_EXTENSION);
    $namaFile = uniqid('artikel_', true) . '.' . $ekstensi;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload gambar baru.']);
        exit;
    }

    // Hapus gambar lama
    if (file_exists($uploadDir . $gambarLama)) {
        @unlink($uploadDir . $gambarLama);
    }

    $gambar = $namaFile;
}

$stmt = $conn->prepare("UPDATE artikel SET judul=?, isi=?, gambar=?, id_penulis=?, id_kategori=? WHERE id=?");
$stmt->bind_param('sssiii', $judul, $isi, $gambar, $id_penulis, $id_kategori, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil diperbarui.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui artikel: ' . $conn->error]);
}

$stmt->close();
$conn->close();
