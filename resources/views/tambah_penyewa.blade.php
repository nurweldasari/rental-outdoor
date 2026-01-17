<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Penyewa</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f7f3ee}
.layout{display:flex;min-height:100vh}

/* SIDEBAR */
.sidebar{
  width:225px;
  background:#fff;
  padding:16px;
  border-right:1px solid #eee;
  transition:width .3s ease
}
.sidebar.collapsed{width:80px}

/* HEADER */
.sidebar-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:16px
}
.logo{margin-left:45px}
.logo-img{width:95px}
.sidebar.collapsed .logo{display:none}

.toggle-btn{
  background:none;
  border:none;
  font-size:20px;
  color:#c56a00;
  cursor:pointer
}

/* NAV */
.sidebar nav a{
  display:flex;
  align-items:center;
  gap:10px;
  padding:9px 10px;
  color:#555;
  text-decoration:none;
  border-radius:10px;
  margin-bottom:6px;
  font-size:14px
}
.sidebar nav a i{
  min-width:18px;
  text-align:center
}
.sidebar nav a:hover,
.sidebar nav a.active{
  background:#f4a340;
  color:#fff
}

/* MENU TITLE */
.menu-title{
  display:flex;
  justify-content:space-between;
  align-items:center;
  font-size:14px;
  color:#999;
  margin:14px 0 5px;
  cursor:pointer
}
.menu-title i{
  font-size:13px;
  transition:.3s
}
.menu-title.open i{transform:rotate(180deg)}

/* COLLAPSE MODE */
.sidebar.collapsed nav a span{display:none}
.sidebar.collapsed .menu-title{display:none}
.sidebar.collapsed nav a{justify-content:center}
.sidebar.collapsed .sidebar-header{justify-content:center}

/* 🔥 FIX PENTING: ICON TIDAK BOLEH HILANG */
.sidebar.collapsed nav a,
.sidebar.collapsed nav a i{
  display:flex !important
}

/* ===== MAIN ===== */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ===== NAVBAR (MENYATU) ===== */
.topbar {
  height: 64px;
  background: #fff;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #eee;
}

/* JUDUL DASHBOARD */
.topbar h2 {
  font-size: 15px;        /* sesuai request */
  font-weight: 400;      /* normal / tidak bold */
  color: #333;
}

/* ===== DROPDOWN USER ===== */
.user-dropdown {
  position: relative;
}

.user-btn {
  background: none;
  border: none;
  color: #c56a00;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}

/* BOX DROPDOWN */
.dropdown-menu {
  position: absolute;
  top: 130%;
  right: 0;
  width: 170px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 14px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.12);
  display: none;
  overflow: hidden;
  z-index: 1000;
}

.dropdown-menu.show {
  display: block;
}

/* ITEM */
.dropdown-menu a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  text-decoration: none;
  color: #555;
  font-size: 14px;
}

.dropdown-menu a i {
  font-size: 18px;
}

/* HOVER */
.dropdown-menu a:hover {
  background: #f7f3ee;
}

/* GARIS PEMISAH */
.dropdown-menu hr {
  border: none;
  border-top: 1px solid #e5e5e5;
}

/* LOGOUT */
.dropdown-menu .logout {
  color: #333;
}
.tambah-penyewa-page {
  padding: 30px;
  display: flex;
  justify-content: center;
}

.form-card {
  width: 100%;
  max-width: 700px;
  background: #fff;
  border-radius: 12px;
  padding: 24px 28px;
  border: 1px solid #ddd;
}

.form-title {
  font-size: 18px;
  margin-bottom: 18px;
  font-weight: 600;
}

.penyewa-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.form-group input {
  width: 100%;
  padding: 12px 18px;
  border-radius: 20px;
  border: 1px solid #333;
  outline: none;
  font-size: 14px;
}

.form-group input:focus {
  border-color: #c56a00;
}

/* Upload Box */
.upload-box {
  margin-top: 10px;
  padding: 30px;
  border: 1px dashed #999;
  border-radius: 12px;
  text-align: center;
  cursor: pointer;
  color: #333;
}

.upload-box i {
  font-size: 28px;
  display: block;
  margin-bottom: 8px;
}

/* Actions */
.form-actions {
  margin-top: 18px;
  display: flex;
  justify-content: center;
  gap: 16px;
}

.btn-submit {
  background: #0a7a2f;
  color: #fff;
  padding: 10px 26px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-cancel {
  background: #e53900;
  color: #fff;
  padding: 10px 26px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

</style>

</head>

<body>
<div class="layout">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="logo">
      <img src="assets/images/logo.png" class="logo-img">
    </div>
    <button class="toggle-btn" id="toggleBtn">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>

  <nav>
    <a href="dashboard_cabang"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
    <a><i class="fa-solid fa-file-contract"></i><span>Kontrak Franchise</span></a>

    <div class="menu-title">Manajemen Penyewa<i class="fa-solid fa-chevron-down"></i></div>
    <a class="active"><i class="fa-solid fa-users"></i><span>Data Penyewa</span></a>

    <div class="menu-title">Manajemen Penyewaan<i class="fa-solid fa-chevron-down"></i></div>
    <a><i class="fa-solid fa-cart-shopping"></i><span>Penyewaan</span></a>
    <a><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat Penyewaan</span></a>
    <a><i class="fa-solid fa-chart-line"></i><span>Laporan Pendapatan</span></a>

    <div class="menu-title">Manajemen Alat<i class="fa-solid fa-chevron-down"></i></div>
    <a><i class="fa-solid fa-toolbox"></i><span>Data Alat</span></a>
    <a><i class="fa-solid fa-layer-group"></i><span>Data Kategori</span></a>
    <a><i class="fa-solid fa-handshake"></i><span>Bagi Hasil</span></a>
  </nav>
</aside>

<!-- MAIN -->
  <main class="main">

    <!-- NAVBAR -->
    <header class="topbar">
      <h2>Dashboard</h2>
      <div class="user-dropdown">
  <button class="user-btn" id="userBtn">
    <span>Admin Cabang</span>
    <i class="fa-solid fa-chevron-down"></i>
  </button>

  <div class="dropdown-menu" id="dropdownMenu">
    <a href="#">
      <i class="fa-solid fa-gear"></i>
      <span>Pengaturan Akun</span>
    </a>
    <hr>
    <a href="#" class="logout">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Logout</span>
    </a>
  </div>
</div>
    </header>

    <div class="tambah-penyewa-page">

  <div class="form-card">
    <h3 class="form-title">Tambah Penyewa</h3>

    <form 
        class="penyewa-form"
        method="POST"
        action="{{ route('tambah_penyewa.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

      <div class="form-group">
        <input type="text" name="nama" placeholder="Nama" required>
      </div>

      <div class="form-group">
        <input type="text" name="username" placeholder="Username" required>
      </div>

      <div class="form-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <div class="form-group">
        <input type="text" name="no_telepon" placeholder="No. Telephone" required>
      </div>

      <div class="form-group">
        <input type="text" name="alamat" placeholder="Alamat" required>
      </div>

      <!-- UPLOAD IDENTITAS -->
      <div class="upload-box" onclick="document.getElementById('gambar').click()">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <span>Upload gambar identitas</span>

        <input 
            type="file"
            name="gambar_identitas"
            id="gambar"
            accept="image/*"
            hidden
            required
        >
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">Tambah Penyewa</button>
        <button type="button" class="btn-cancel" onclick="history.back()">Batal</button>
      </div>

    </form>
  </div>

</div>


    <script>
        //Dropdown Sidebar
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');

/* COLLAPSE */
toggleBtn.onclick = () => sidebar.classList.toggle('collapsed');

/* DROPDOWN  menu*/
document.querySelectorAll('.menu-title').forEach(title=>{
  title.addEventListener('click',()=>{
    if(sidebar.classList.contains('collapsed')) return;

    let open = title.classList.toggle('open');
    let next = title.nextElementSibling;

    while(next && !next.classList.contains('menu-title')){
      next.style.display = open ? 'none' : 'flex';
      next = next.nextElementSibling;
    }
  });
});
// Dropdown pengaturan akun
const userBtn = document.getElementById('userBtn');
const dropdownMenu = document.getElementById('dropdownMenu');

userBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  dropdownMenu.classList.toggle('show');
});

document.addEventListener('click', () => {
  dropdownMenu.classList.remove('show');
});
</script>
</body>
</html>