<?php
header('Content-Type: application/json');
require_once 'koneksi.php';
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { echo json_encode(['status'=>'error','message'=>'ID tidak valid.']); exit; }
$stmt = $conn->prepare("SELECT id, nama_kategori FROM kategori WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { echo json_encode(['status'=>'error','message'=>'Kategori tidak ditemukan.']); exit; }
echo json_encode(['status'=>'success','data'=>$result->fetch_assoc()]);
$stmt->close(); $conn->close();
