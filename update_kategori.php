<?php include 'koneksi.php';
$stmt=$koneksi->prepare("UPDATE kategori_artikel SET nama_kategori=?, keterangan=? WHERE id=?");
$stmt->bind_param('ssi',$_POST['nama_kategori'],$_POST['keterangan'],$_POST['id']);
$stmt->execute();
?>