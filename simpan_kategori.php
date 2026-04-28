<?php include 'koneksi.php';
$stmt=$koneksi->prepare("INSERT INTO kategori_artikel(nama_kategori,keterangan) VALUES(?,?)");
$stmt->bind_param('ss',$_POST['nama_kategori'],$_POST['keterangan']);
$stmt->execute();
?>