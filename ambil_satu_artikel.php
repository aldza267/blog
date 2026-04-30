<?php
// ambil_satu_artikel.php - Mengambil satu data artikel berdasarkan ID
header('Content-Type: application/json');
require_once 'koneksi.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    exit;
}

$sql = "SELECT 
            a.id,
            a.judul,
            a.isi,
            a.gambar,
            a.hari_tanggal,
            a.id_penulis,
            a.id_kategori,
            p.nama AS nama_penulis,
            k.nama_kategori
        FROM artikel a
        LEFT JOIN penulis p ON a.id_penulis = p.id
        LEFT JOIN kategori k ON a.id_kategori = k.id
        WHERE a.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'data' => $data]);
