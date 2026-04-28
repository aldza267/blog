<?php include 'koneksi.php';
$stmt=$koneksi->prepare("UPDATE artikel SET judul=?, isi=?, id_penulis=?, id_kategori=? WHERE id=?");
$stmt->bind_param('ssiii',$_POST['judul'],$_POST['isi'],$_POST['id_penulis'],$_POST['id_kategori'],$_POST['id']);
$stmt->execute();
?>