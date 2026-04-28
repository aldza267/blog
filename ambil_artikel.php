<?php include 'koneksi.php';
$sql="SELECT artikel.*,penulis.nama_depan,kategori_artikel.nama_kategori FROM artikel JOIN penulis ON artikel.id_penulis=penulis.id JOIN kategori_artikel ON artikel.id_kategori=kategori_artikel.id";
$q=$koneksi->query($sql);
echo "<h3>Data Artikel</h3><table border='1' width='100%'><tr><th>Judul</th><th>Kategori</th><th>Penulis</th></tr>";
while($d=$q->fetch_assoc()){
echo "<tr><td>$d[judul]</td><td>$d[nama_kategori]</td><td>$d[nama_depan]</td></tr>";
}
echo "</table>";
?>