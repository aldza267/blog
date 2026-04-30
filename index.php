<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Sistem manajemen blog dengan fitur CRUD Penulis, Kategori, dan Artikel.">
<title>Blog Admin Panel - UTS Web</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
  :root {
    --bg: #f7f8f0; --surface: #95DCE4; --card: #89618E;
    --border: #248C54; --accent: #248C54; --accent2: #89618E;
    --text: #248C54; --muted: #89618E; --success: #43d9ad; --danger: #ff6b6b;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

  /* Sidebar */
  #sidebar { width:240px; background:var(--surface); border-right:1px solid var(--border); display:flex; flex-direction:column; padding:24px 16px; gap:8px; min-height:100vh; }
  #sidebar h1 { font-size:1.1rem; font-weight:700; color:var(--accent); padding:0 8px 16px; border-bottom:1px solid var(--border); margin-bottom:8px; }
  .nav-btn { background:none; border:none; color:var(--muted); padding:10px 12px; border-radius:8px; cursor:pointer; text-align:left; font-size:.9rem; font-family:inherit; transition:.2s; width:100%; }
  .nav-btn:hover, .nav-btn.active { background:var(--accent); color:#fff; }

  /* Main */
  #main { flex:1; padding:32px; overflow-y:auto; }
  #main h2 { font-size:1.4rem; font-weight:600; margin-bottom:20px; }

  /* Table */
  .table-wrap { background: rgba(149, 220, 228, .16); border-radius:12px; border:1px solid var(--border); overflow:hidden; }
  table { width:100%; border-collapse:collapse; }
  thead tr { background:#95DCE4; }
  th, td { padding:12px 16px; text-align:left; font-size:.85rem; border-bottom:1px solid rgba(255,255,255,.18); color:#fff; }
  th { color:#fff; font-weight:600; text-transform:uppercase; font-size:.75rem; letter-spacing:.05em; }
  td img { width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--accent); }
  td img.artikel-img { border-radius:6px; width:60px; height:40px; }
  .table-wrap table tbody tr { background:#89618E; }
  .table-wrap table tbody tr:nth-child(even) { background:#7d5280; }
  tr:last-child td { border-bottom:none; }
  tr:hover td { background:rgba(255,255,255,.18); }

  /* Buttons */
  .btn { padding:8px 16px; border:none; border-radius:8px; cursor:pointer; font-size:.83rem; font-weight:500; font-family:inherit; transition:.2s; }
  .btn-accent { background:var(--accent); color:#fff; }
  .btn-accent:hover { background:#1f7646; }
  .btn-success { background:var(--success); color:#000; }
  .btn-danger  { background:var(--danger); color:#fff; }
  .btn-sm { padding:5px 10px; font-size:.78rem; }
  #add-btn { margin-bottom:16px; }

  /* Modal */
  .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:100; align-items:center; justify-content:center; }
  .modal-bg.open { display:flex; }
  .modal { background:var(--card); border:1px solid var(--border); border-radius:16px; padding:28px; width:500px; max-width:95vw; max-height:90vh; overflow-y:auto; }
  .modal h3 { font-size:1.1rem; margin-bottom:20px; color:var(--accent); }
  .form-group { margin-bottom:14px; }
  .form-group label { display:block; font-size:.82rem; color:var(--muted); margin-bottom:5px; }
  .form-group input, .form-group select, .form-group textarea {
    width:100%; background:var(--surface); border:1px solid var(--border); border-radius:8px;
    color:var(--text); padding:9px 12px; font-size:.88rem; font-family:inherit; outline:none; transition:.2s;
  }
  .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--accent); }
  .form-group textarea { resize:vertical; min-height:100px; }
  .modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:18px; }

  /* Alert */
  #alert-box { position:fixed; top:20px; right:20px; z-index:200; display:flex; flex-direction:column; gap:8px; }
  .alert { padding:12px 18px; border-radius:10px; font-size:.85rem; font-weight:500; animation:fadeIn .3s; max-width:320px; }
  .alert-success { background:#1a3a2e; border:1px solid var(--success); color:var(--success); }
  .alert-error   { background:#3a1a1a; border:1px solid var(--danger); color:var(--danger); }
  @keyframes fadeIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:none; } }
  .empty-state { text-align:center; color:var(--muted); padding:40px; }
</style>
</head>
<body>

<nav id="sidebar">
  <h1>📝 Blog Admin</h1>
  <button class="nav-btn active" onclick="loadMenu('penulis')" id="nav-penulis">👤 Penulis</button>
  <button class="nav-btn" onclick="loadMenu('kategori')" id="nav-kategori">🏷️ Kategori</button>
  <button class="nav-btn" onclick="loadMenu('artikel')" id="nav-artikel">📄 Artikel</button>
</nav>

<main id="main">
  <h2 id="page-title">Manajemen Penulis</h2>
  <button class="btn btn-accent" id="add-btn" onclick="openModal()">+ Tambah Data</button>
  <div class="table-wrap">
    <table id="data-table">
      <thead id="table-head"></thead>
      <tbody id="table-body"></tbody>
    </table>
  </div>
</main>

<div class="modal-bg" id="modal-bg">
  <div class="modal">
    <h3 id="modal-title">Tambah Data</h3>
    <form id="modal-form" enctype="multipart/form-data">
      <input type="hidden" id="form-id" name="id">
      <div id="form-fields"></div>
      <div class="modal-actions">
        <button type="button" class="btn btn-danger" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn btn-success" id="form-submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div id="alert-box"></div>

<script>
let currentMenu = 'penulis';
let editId = null;

// ─── Navigation ───────────────────────────────────────────────────────────────
function loadMenu(menu) {
  currentMenu = menu;
  document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('nav-' + menu).classList.add('active');

  const titles = { penulis:'Manajemen Penulis', kategori:'Manajemen Kategori', artikel:'Manajemen Artikel' };
  document.getElementById('page-title').textContent = titles[menu];

  if (menu === 'penulis') {
    document.getElementById('table-head').innerHTML = `<tr><th>#</th><th>Foto</th><th>Nama</th><th>Email</th><th>Aksi</th></tr>`;
  } else if (menu === 'kategori') {
    document.getElementById('table-head').innerHTML = `<tr><th>#</th><th>Nama Kategori</th><th>Aksi</th></tr>`;
  } else {
    document.getElementById('table-head').innerHTML = `<tr><th>#</th><th>Gambar</th><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Tanggal</th><th>Aksi</th></tr>`;
  }

  fetchData(menu);
}

// ─── Fetch & Render Data ──────────────────────────────────────────────────────
async function fetchData(menu) {
  const urls = { penulis:'ambil_penulis.php', kategori:'ambil_kategori.php', artikel:'ambil_artikel.php' };
  try {
    const res  = await fetch(urls[menu]);
    const json = await res.json();
    const tbody = document.getElementById('table-body');

    if (!json.data || json.data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">Belum ada data.</div></td></tr>`;
      return;
    }

    tbody.innerHTML = json.data.map((row, i) => {
      if (menu === 'penulis') {
        const foto = row.foto && row.foto !== 'default.png'
          ? `uploads_penulis/${row.foto}`
          : `uploads_penulis/default.png`;
        return `<tr>
          <td>${i+1}</td>
          <td><img src="${foto}" alt="${row.nama}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(row.nama)}&background=6c63ff&color=fff'"></td>
          <td>${row.nama}</td>
          <td>${row.email}</td>
          <td>${actionBtns(row.id)}</td></tr>`;
      } else if (menu === 'kategori') {
        return `<tr><td>${i+1}</td><td>${row.nama_kategori}</td><td>${actionBtns(row.id)}</td></tr>`;
      } else {
        return `<tr>
          <td>${i+1}</td>
          <td><img class="artikel-img" src="uploads_artikel/${row.gambar}" alt="${row.judul}" onerror="this.src='https://placehold.co/60x40/21253a/6c63ff?text=IMG'"></td>
          <td>${row.judul}</td>
          <td>${row.nama_penulis ?? '-'}</td>
          <td>${row.nama_kategori ?? '-'}</td>
          <td style="font-size:.78rem;color:var(--muted)">${row.hari_tanggal}</td>
          <td>${actionBtns(row.id)}</td></tr>`;
      }
    }).join('');
  } catch(e) {
    showAlert('Gagal memuat data: ' + e.message, 'error');
  }
}

function actionBtns(id) {
  return `<div style="display:flex;gap:6px">
    <button class="btn btn-accent btn-sm" onclick="openEdit(${id})">Edit</button>
    <button class="btn btn-danger btn-sm"  onclick="hapusData(${id})">Hapus</button>
  </div>`;
}

// ─── Modal ────────────────────────────────────────────────────────────────────
function openModal() {
  editId = null;
  document.getElementById('modal-title').textContent = 'Tambah ' + currentMenu.charAt(0).toUpperCase() + currentMenu.slice(1);
  document.getElementById('modal-form').reset();
  document.getElementById('form-id').value = '';
  buildFormFields();
  document.getElementById('modal-bg').classList.add('open');
}

function closeModal() {
  document.getElementById('modal-bg').classList.remove('open');
}

function buildFormFields(data = {}) {
  const c = document.getElementById('form-fields');
  if (currentMenu === 'penulis') {
    c.innerHTML = `
      <div class="form-group"><label>Nama</label><input name="nama" value="${data.nama||''}" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="${data.email||''}" required></div>
      <div class="form-group"><label>Password ${editId?'(kosongkan jika tidak diubah)':''}</label><input type="password" name="password" ${editId?'':'required'}></div>
      <div class="form-group"><label>Foto (maks 2MB)</label><input type="file" name="foto" accept="image/*"></div>`;
  } else if (currentMenu === 'kategori') {
    c.innerHTML = `<div class="form-group"><label>Nama Kategori</label><input name="nama_kategori" value="${data.nama_kategori||''}" required></div>`;
  } else {
    c.innerHTML = `
      <div class="form-group"><label>Judul</label><input name="judul" value="${data.judul||''}" required></div>
      <div class="form-group"><label>Isi Artikel</label><textarea name="isi" required>${data.isi||''}</textarea></div>
      <div class="form-group"><label>Penulis</label><select name="id_penulis" id="sel-penulis" required><option value="">-- Pilih Penulis --</option></select></div>
      <div class="form-group"><label>Kategori</label><select name="id_kategori" id="sel-kategori" required><option value="">-- Pilih Kategori --</option></select></div>
      <div class="form-group"><label>Gambar ${editId?'(kosongkan jika tidak diubah)':'(wajib, maks 2MB)'}</label><input type="file" name="gambar" accept="image/*" ${editId?'':'required'}></div>`;
    loadDropdowns(data);
  }
}

async function loadDropdowns(data = {}) {
  const [resPenulis, resKategori] = await Promise.all([
    fetch('ambil_penulis.php').then(r => r.json()),
    fetch('ambil_kategori.php').then(r => r.json())
  ]);
  const selP = document.getElementById('sel-penulis');
  const selK = document.getElementById('sel-kategori');
  if (selP && resPenulis.data) resPenulis.data.forEach(p => {
    selP.innerHTML += `<option value="${p.id}" ${data.id_penulis==p.id?'selected':''}>${p.nama}</option>`;
  });
  if (selK && resKategori.data) resKategori.data.forEach(k => {
    selK.innerHTML += `<option value="${k.id}" ${data.id_kategori==k.id?'selected':''}>${k.nama_kategori}</option>`;
  });
}

async function openEdit(id) {
  editId = id;
  const urls = { penulis:`ambil_satu_penulis.php?id=${id}`, kategori:`ambil_satu_kategori.php?id=${id}`, artikel:`ambil_satu_artikel.php?id=${id}` };
  const res  = await fetch(urls[currentMenu]);
  const json = await res.json();
  if (json.status !== 'success') { showAlert(json.message,'error'); return; }
  document.getElementById('modal-title').textContent = 'Edit ' + currentMenu.charAt(0).toUpperCase() + currentMenu.slice(1);
  document.getElementById('form-id').value = id;
  buildFormFields(json.data);
  document.getElementById('modal-bg').classList.add('open');
}

// ─── Submit Form ──────────────────────────────────────────────────────────────
document.getElementById('modal-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const isEdit = !!editId;
  const endpoints = {
    penulis:   { add:'simpan_penulis.php',   edit:'update_penulis.php'   },
    kategori:  { add:'simpan_kategori.php',  edit:'update_kategori.php'  },
    artikel:   { add:'simpan_artikel.php',   edit:'update_artikel.php'   },
  };
  const url = isEdit ? endpoints[currentMenu].edit : endpoints[currentMenu].add;
  const fd  = new FormData(this);

  try {
    const res  = await fetch(url, { method:'POST', body:fd });
    const json = await res.json();
    if (json.status === 'success') {
      showAlert(json.message, 'success');
      closeModal();
      fetchData(currentMenu);
    } else {
      showAlert(json.message, 'error');
    }
  } catch(err) {
    showAlert('Terjadi kesalahan: ' + err.message, 'error');
  }
});

// ─── Hapus ────────────────────────────────────────────────────────────────────
async function hapusData(id) {
  if (!confirm('Yakin ingin menghapus data ini?')) return;
  const urls = { penulis:'hapus_penulis.php', kategori:'hapus_kategori.php', artikel:'hapus_artikel.php' };
  const fd = new FormData(); fd.append('id', id);
  const res  = await fetch(urls[currentMenu], { method:'POST', body:fd });
  const json = await res.json();
  showAlert(json.message, json.status === 'success' ? 'success' : 'error');
  if (json.status === 'success') fetchData(currentMenu);
}

// ─── Alert ────────────────────────────────────────────────────────────────────
function showAlert(msg, type) {
  const box   = document.getElementById('alert-box');
  const el    = document.createElement('div');
  el.className = `alert alert-${type}`;
  el.textContent = msg;
  box.appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

// Tutup modal klik luar
document.getElementById('modal-bg').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// Init
loadMenu('penulis');
</script>
</body>
</html>
