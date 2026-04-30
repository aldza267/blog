<?php
// simpan_penulis.php - Menyimpan data penulis baru
header('Content-Type: application/json');
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input wajib
if (empty($nama) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Nama, email, dan password wajib diisi.']);
    exit;
}

// Enkripsi password menggunakan PASSWORD_BCRYPT
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Proses upload foto
$foto = 'default.png';

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $file     = $_FILES['foto'];
    $maxSize  = 2 * 1024 * 1024; // 2 MB

    // Validasi ukuran file
    if ($file['size'] > $maxSize) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran foto melebihi 2 MB.']);
        exit;
    }

    // Validasi tipe MIME menggunakan finfo
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe file tidak diizinkan. Hanya JPEG, PNG, GIF, WEBP.']);
        exit;
    }

    // Generate nama file unik
    $ekstensi = pathinfo($file['name'], PATHINFO_EXTENSION);
    $namaFile = uniqid('penulis_', true) . '.' . $ekstensi;
    $uploadDir = __DIR__ . '/uploads_penulis/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload foto.']);
        exit;
    }

    $foto = $namaFile;
}

// Simpan ke database menggunakan prepared statements
$stmt = $conn->prepare("INSERT INTO penulis (nama, email, password, foto) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $nama, $email, $password_hash, $foto);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Penulis berhasil ditambahkan.']);
} else {
    // Jika gagal, hapus foto yang sudah terupload
    if ($foto !== 'default.png') {
        @unlink(__DIR__ . '/uploads_penulis/' . $foto);
    }
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $conn->error]);
}

$stmt->close();
$conn->close();
