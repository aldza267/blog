<?php include 'koneksi.php';
$stmt=$koneksi->prepare("UPDATE penulis SET nama_depan=?, nama_belakang=?, user_name=? WHERE id=?");
$stmt->bind_param('sssi',$_POST['nama_depan'],$_POST['nama_belakang'],$_POST['user_name'],$_POST['id']);
$stmt->execute();
?>