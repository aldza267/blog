<?php include 'koneksi.php';
$stmt=$koneksi->prepare("DELETE FROM kategori_artikel WHERE id=?");
$stmt->bind_param('i',$_POST['id']);
$stmt->execute();
?>