<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

$stmt = $conn->prepare("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
$stmt->execute();
$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) $data[] = $row;
$stmt->close(); $conn->close();
echo json_encode(['status' => 'success', 'data' => $data]);
