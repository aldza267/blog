<?php include 'koneksi.php';
$pass=password_hash($_POST['password'],PASSWORD_BCRYPT);
$stmt=$koneksi->prepare("INSERT INTO penulis(nama_depan,nama_belakang,user_name,password,foto) VALUES(?,?,?,?,?)");
$foto='default.png';
$stmt->bind_param('sssss',$_POST['nama_depan'],$_POST['nama_belakang'],$_POST['user_name'],$pass,$foto);
$stmt->execute();
?>