<?php
header('Content-Type: application/json');
require_once 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status'=>'error','message'=>'Method tidak diizinkan.']); exit; }
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) { echo json_encode(['status'=>'error','message'=>'ID tidak valid.']); exit; }
$cek = $conn->prepare("SELECT COUNT(*) AS total FROM artikel WHERE id_kategori = ?");
$cek->bind_param('i', $id);
$cek->execute();
$hasil = $cek->get_result()->fetch_assoc();
$cek->close();
if ($hasil['total'] > 0) { echo json_encode(['status'=>'error','message'=>'Kategori tidak dapat dihapus, masih digunakan oleh '.$hasil['total'].' artikel.']); exit; }
$stmt = $conn->prepare("DELETE FROM kategori WHERE id = ?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Kategori berhasil dihapus.']);
else echo json_encode(['status'=>'error','message'=>'Gagal: '.$conn->error]);
$stmt->close(); $conn->close();
