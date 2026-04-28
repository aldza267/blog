<?php include 'koneksi.php';
$q=$koneksi->query("SELECT * FROM kategori_artikel");
echo "<h3>Data Kategori</h3><table border='1' width='100%'><tr><th>Nama</th><th>Keterangan</th></tr>";
while($d=$q->fetch_assoc()){
echo "<tr><td>$d[nama_kategori]</td><td>$d[keterangan]</td></tr>";
}
echo "</table>";
?>