<?php
header('Content-Type: application/json');
require_once 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status'=>'error','message'=>'Method tidak diizinkan.']); exit; }
$nama = trim($_POST['nama_kategori'] ?? '');
if (empty($nama)) { echo json_encode(['status'=>'error','message'=>'Nama kategori wajib diisi.']); exit; }
$stmt = $conn->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
$stmt->bind_param('s', $nama);
if ($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Kategori berhasil ditambahkan.']);
else echo json_encode(['status'=>'error','message'=>'Gagal: '.$conn->error]);
$stmt->close(); $conn->close();
