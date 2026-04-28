<?php include 'koneksi.php';
$q=$koneksi->query("SELECT * FROM penulis ORDER BY id DESC");
echo "<h3>Data Penulis</h3><table border='1' width='100%'><tr><th>Foto</th><th>Nama</th><th>Username</th></tr>";
while($d=$q->fetch_assoc()){
$foto=$d['foto']?:'default.png';
echo "<tr><td><img src='uploads_penulis/$foto' width='50'></td><td>$d[nama_depan] $d[nama_belakang]</td><td>$d[user_name]</td></tr>";
}
echo "</table>";
?>