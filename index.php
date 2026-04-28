<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog CMS Dashboard</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f4f7fb;
    color:#222;
}

.wrapper{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    background:linear-gradient(180deg,#111827,#1e293b);
    color:white;
    padding:25px;
    box-shadow:4px 0 20px rgba(0,0,0,0.08);
}

.logo{
    font-size:24px;
    font-weight:700;
    margin-bottom:35px;
}

.menu button{
    width:100%;
    border:none;
    padding:14px;
    margin-bottom:12px;
    border-radius:12px;
    background:rgba(255,255,255,0.08);
    color:white;
    font-size:15px;
    cursor:pointer;
    transition:0.3s;
}

.menu button:hover{
    background:#6366f1;
    transform:translateX(5px);
}

/* MAIN */
.main{
    flex:1;
    padding:30px;
}

/* TOPBAR */
.topbar{
    background:linear-gradient(90deg,#4f46e5,#7c3aed);
    color:white;
    padding:22px 30px;
    border-radius:18px;
    font-size:26px;
    font-weight:700;
    margin-bottom:25px;
    box-shadow:0 10px 30px rgba(79,70,229,0.25);
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.card{
    background:white;
    padding:22px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    font-size:16px;
    color:#777;
    margin-bottom:8px;
}

.card p{
    font-size:28px;
    font-weight:700;
    color:#111827;
}

/* CONTENT */
.content{
    background:white;
    border-radius:18px;
    padding:25px;
    min-height:400px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

/* BUTTON DARK */
.dark-btn{
    margin-top:20px;
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#0ea5e9;
    color:white;
    cursor:pointer;
    font-weight:600;
}

.dark-btn:hover{
    background:#0284c7;
}

/* DARK MODE */
.dark{
    background:#0f172a;
    color:white;
}

.dark .content,
.dark .card{
    background:#1e293b;
    color:white;
}

.dark .card h3{
    color:#cbd5e1;
}

.dark .card p{
    color:white;
}

/* MOBILE */
@media(max-width:768px){
    .wrapper{
        flex-direction:column;
    }

    .sidebar{
        width:100%;
    }
}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">🚀 Blog CMS</div>

        <div class="menu">
            <button onclick="loadPage('ambil_penulis.php')">👤 Kelola Penulis</button>
            <button onclick="loadPage('ambil_artikel.php')">📰 Kelola Artikel</button>
            <button onclick="loadPage('ambil_kategori.php')">📂 Kelola Kategori</button>
        </div>

        <button class="dark-btn" onclick="toggleDark()">🌙 Dark Mode</button>
    </div>

    <!-- MAIN -->
    <div class="main">

        <div class="topbar">
            Sistem Manajemen Blog (CMS)
        </div>

        <?php
        $jmlPenulis = $koneksi->query("SELECT * FROM penulis")->num_rows;
        $jmlArtikel = $koneksi->query("SELECT * FROM artikel")->num_rows;
        $jmlKategori = $koneksi->query("SELECT * FROM kategori_artikel")->num_rows;
        ?>

        <div class="stats">
            <div class="card">
                <h3>Total Penulis</h3>
                <p><?= $jmlPenulis ?></p>
            </div>

            <div class="card">
                <h3>Total Artikel</h3>
                <p><?= $jmlArtikel ?></p>
            </div>

            <div class="card">
                <h3>Total Kategori</h3>
                <p><?= $jmlKategori ?></p>
            </div>
        </div>

        <div class="content" id="konten">
            <h2>Selamat Datang 👋</h2>
            <p style="margin-top:10px;color:#666;">
                Gunakan menu di sebelah kiri untuk mengelola data blog Anda.
            </p>
        </div>

    </div>

</div>

<script>
function loadPage(url){
    fetch(url)
    .then(res => res.text())
    .then(data => {
        document.getElementById("konten").innerHTML = data;
    });
}

function toggleDark(){
    document.body.classList.toggle("dark");
}
</script>

</body>
</html>