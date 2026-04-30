<?php
header('Content-Type: application/json');
require_once 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status'=>'error','message'=>'Method tidak diizinkan.']); exit; }
$id   = intval($_POST['id'] ?? 0);
$nama = trim($_POST['nama_kategori'] ?? '');
if ($id <= 0 || empty($nama)) { echo json_encode(['status'=>'error','message'=>'ID dan nama kategori wajib diisi.']); exit; }
$stmt = $conn->prepare("UPDATE kategori SET nama_kategori=? WHERE id=?");
$stmt->bind_param('si', $nama, $id);
if ($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Kategori berhasil diperbarui.']);
else echo json_encode(['status'=>'error','message'=>'Gagal: '.$conn->error]);
$stmt->close(); $conn->close();
