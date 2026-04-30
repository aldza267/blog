<?php
// simpan_artikel.php - Menyimpan data artikel baru
header('Content-Type: application/json');
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$judul       = trim($_POST['judul'] ?? '');
$isi         = trim($_POST['isi'] ?? '');
$id_penulis  = intval($_POST['id_penulis'] ?? 0);
$id_kategori = intval($_POST['id_kategori'] ?? 0);

// Validasi input wajib
if (empty($judul) || empty($isi) || $id_penulis <= 0 || $id_kategori <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
    exit;
}

// Gambar wajib diupload untuk artikel
if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Gambar artikel wajib diunggah.']);
    exit;
}

$file    = $_FILES['gambar'];
$maxSize = 2 * 1024 * 1024; // 2 MB

// Validasi ukuran file
if ($file['size'] > $maxSize) {
    echo json_encode(['status' => 'error', 'message' => 'Ukuran gambar melebihi 2 MB.']);
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
$ekstensi  = pathinfo($file['name'], PATHINFO_EXTENSION);
$namaFile  = uniqid('artikel_', true) . '.' . $ekstensi;
$uploadDir = __DIR__ . '/uploads_artikel/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload gambar.']);
    exit;
}

// Generate kolom hari_tanggal secara otomatis dari sisi server dengan timezone Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

$hariInggris = date('l');
$hariMap = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
];
$bulanMap = [
    1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
    4  => 'April',     5  => 'Mei',      6  => 'Juni',
    7  => 'Juli',      8  => 'Agustus',  9  => 'September',
    10 => 'Oktober',   11 => 'November', 12 => 'Desember',
];

$hariIndonesia = $hariMap[$hariInggris];
$tanggal       = date('j');
$bulan         = $bulanMap[(int)date('n')];
$tahun         = date('Y');
$jam           = date('H:i');

$hari_tanggal = "$hariIndonesia, $tanggal $bulan $tahun | $jam";

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO artikel (judul, isi, gambar, hari_tanggal, id_penulis, id_kategori) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssii', $judul, $isi, $namaFile, $hari_tanggal, $id_penulis, $id_kategori);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil ditambahkan.']);
} else {
    // Rollback: hapus gambar jika gagal simpan ke DB
    @unlink($uploadDir . $namaFile);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan artikel: ' . $conn->error]);
}

$stmt->close();
$conn->close();
