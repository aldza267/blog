<?php
// ambil_penulis.php - Mengambil semua data penulis
header('Content-Type: application/json');
require_once 'koneksi.php';

$stmt = $conn->prepare("SELECT id, nama, email, foto FROM penulis ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'data' => $data]);
