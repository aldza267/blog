<?php include 'koneksi.php';
$stmt=$koneksi->prepare("DELETE FROM artikel WHERE id=?");
$stmt->bind_param('i',$_POST['id']);
$stmt->execute();
?>