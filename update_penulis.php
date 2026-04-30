<?php
// update_penulis.php - Memperbarui data penulis
header('Content-Type: application/json');
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$id    = intval($_POST['id'] ?? 0);
$nama  = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($id <= 0 || empty($nama) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'ID, nama, dan email wajib diisi.']);
    exit;
}

// Ambil data lama untuk keperluan foto
$stmtCek = $conn->prepare("SELECT foto FROM penulis WHERE id = ?");
$stmtCek->bind_param('i', $id);
$stmtCek->execute();
$hasilCek = $stmtCek->get_result();

if ($hasilCek->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Penulis tidak ditemukan.']);
    exit;
}

$dataLama  = $hasilCek->fetch_assoc();
$fotoLama  = $dataLama['foto'];
$stmtCek->close();

$foto = $fotoLama; // Default pakai foto lama

// Proses upload foto baru jika ada
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $file    = $_FILES['foto'];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    if ($file['size'] > $maxSize) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran foto melebihi 2 MB.']);
        exit;
    }

    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe file tidak diizinkan. Hanya JPEG, PNG, GIF, WEBP.']);
        exit;
    }

    $ekstensi  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $namaFile  = uniqid('penulis_', true) . '.' . $ekstensi;
    $uploadDir = __DIR__ . '/uploads_penulis/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload foto baru.']);
        exit;
    }

    // Hapus foto lama jika bukan default.png
    if ($fotoLama !== 'default.png' && file_exists($uploadDir . $fotoLama)) {
        @unlink($uploadDir . $fotoLama);
    }

    $foto = $namaFile;
}

// Proses update password jika diisi
$password = $_POST['password'] ?? '';

if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE penulis SET nama=?, email=?, password=?, foto=? WHERE id=?");
    $stmt->bind_param('ssssi', $nama, $email, $password_hash, $foto, $id);
} else {
    $stmt = $conn->prepare("UPDATE penulis SET nama=?, email=?, foto=? WHERE id=?");
    $stmt->bind_param('sssi', $nama, $email, $foto, $id);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data penulis berhasil diperbarui.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $conn->error]);
}

$stmt->close();
$conn->close();
