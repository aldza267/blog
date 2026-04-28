<?php include 'koneksi.php';
$stmt=$koneksi->prepare("DELETE FROM penulis WHERE id=?");
$stmt->bind_param('i',$_POST['id']);
$stmt->execute();
?>