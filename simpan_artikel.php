<?php include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$tanggal=date('l, d-m-Y H:i');
$gambar='gambar.jpg';
$stmt=$koneksi->prepare("INSERT INTO artikel(id_penulis,id_kategori,judul,isi,gambar,hari_tanggal) VALUES(?,?,?,?,?,?)");
$stmt->bind_param('iissss',$_POST['id_penulis'],$_POST['id_kategori'],$_POST['judul'],$_POST['isi'],$gambar,$tanggal);
$stmt->execute();
?>