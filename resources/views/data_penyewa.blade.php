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

/* PAGE CONTAINER */
.page-container {
  background: #f7f3ee;
  padding: 20px;
}

/* HEADER */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

/* SEARCH */
.search-box {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 8px 14px;
  border-radius: 12px;
  border: 1px solid #ddd;
  width: 260px;
}

.search-box i {
  color: #777;
  margin-right: 8px;
}

.search-box input {
  border: none;
  outline: none;
  width: 100%;
}

/* ADD BUTTON */
.btn-add {
  background: #d98200;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
}

/* TABLE */
.table-wrapper {
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  border: 1px solid #aaa;
}

.table-top {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
}

.table-top select {
  padding: 4px 6px;
}

/* TABLE STYLE */
table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f1f1f1;
}

th, td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #aaa;
}

th {
  font-weight: 600;
}

/* AKSI */
.aksi {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.btn-green {
  background: #0b6e2b;
  color: #fff;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
}

.btn-yellow {
  background: #ffb000;
  color: #fff;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
}

/* PAGINATION */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-top: 20px;
  color: #7a3e00;
}

.pagination span {
  cursor: pointer;
}

.pagination .active {
  font-weight: 700;
}

/* OVERLAY */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

/* BOX */
.modal-box {
  width: 700px;
  background: #fff;
  border-radius: 12px;
  padding: 20px;
}

/* HEADER */
.modal-header {
  background: #ffd7b5;
  color: #000;
  padding: 8px 16px;
  border-radius: 8px;
  text-align: center;
  font-weight: 600;
  margin-bottom: 20px;
}

/* BODY */
.modal-body {
  display: flex;
  gap: 24px;
}

/* LEFT */
.modal-left {
  width: 40%;
  text-align: center;
}

.modal-left img {
  width: 100%;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.modal-left p {
  margin-top: 8px;
  font-size: 14px;
}

/* RIGHT */
.modal-right {
  width: 60%;
}

.form-group {
  margin-bottom: 12px;
}

.form-group label {
  display: block;
  font-size: 14px;
  margin-bottom: 4px;
}

.form-group input {
  width: 100%;
  padding: 8px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: #f9f9f9;
}

/* FOOTER */
.modal-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

.btn-close {
  background: #e53935;
  color: #fff;
  border: none;
  padding: 8px 20px;
  border-radius: 6px;
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
<div class="page-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Pencarian...">
        </div>

        <a href="/tambah_penyewa" class="btn-add">
    <i class="fa-solid fa-plus"></i>
    Tambah Penyewa
</a>

    </div>

    <!-- TABLE -->
    <div class="table-wrapper">

        <div class="table-top">
            <select>
                <option>10</option>
                <option>50</option>
                <option selected>100</option>
            </select>
            <span>Data Per Halaman</span>
        </div>

        <table>

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($penyewa as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->alamat }}</td>
                        <td>{{ $p->no_telepon }}</td>
                        <td class="aksi">
                            <button class="btn-green">Buat Reservasi</button>
                            <button 
                            type="button"
                            class="btn-yellow btn-detail"
                            data-nama="{{ $p->nama }}"
                            data-username="{{ $p->username }}"
                            data-telepon="{{ $p->no_telepon }}"
                            data-alamat="{{ $p->alamat }}"
                            data-foto="{{ asset('assets/uploads/identitas/'.$p->gambar_identitas) }}">
                            Detail
                        </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:20px">
                            Data penyewa belum tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

        <!-- PAGINATION -->
        <div class="pagination">
            <i class="fa-solid fa-angles-left"></i>
            <span class="active">1</span>
            <span>2</span>
            <span>3</span>
            <i class="fa-solid fa-angles-right"></i>
        </div>

    </div>
</div>
<!-- MODAL OVERLAY -->
<div class="modal-overlay" id="modalDetailPenyewa">

  <div class="modal-box">

    <!-- HEADER -->
    <div class="modal-header">
      <span>Identitas Penyewa</span>
    </div>

    <!-- BODY -->
    <div class="modal-body">

      <!-- KIRI -->
      <div class="modal-left">
        <img id="modalFoto"
             src=""
             alt="Gambar Identitas">
        <p>Gambar Identitas</p>
      </div>

      <!-- KANAN -->
      <div class="modal-right">

        <div class="form-group">
          <label>Nama</label>
          <input type="text" id="modalNama" disabled>
        </div>

        <div class="form-group">
          <label>Username</label>
          <input type="text" id="modalUsername" disabled>
        </div>

        <div class="form-group">
          <label>No. Telephone</label>
          <input type="text" id="modalTelepon" disabled>
        </div>

        <div class="form-group">
          <label>Alamat</label>
          <input type="text" id="modalAlamat" disabled>
        </div>

      </div>

    </div>

    <!-- FOOTER -->
    <div class="modal-footer">
      <button class="btn-close" onclick="closeModal()">Tutup</button>
    </div>

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
// Modal Detail
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".btn-detail").forEach(button => {

        button.addEventListener("click", function () {

            document.getElementById("modalNama").value =
                this.dataset.nama;

            document.getElementById("modalUsername").value =
                this.dataset.username;

            document.getElementById("modalTelepon").value =
                this.dataset.telepon;

            document.getElementById("modalAlamat").value =
                this.dataset.alamat;

            document.getElementById("modalFoto").src =
                this.dataset.foto;

            document.getElementById("modalDetailPenyewa").style.display =
                "flex";
        });

    });

});

function closeModal() {
    document.getElementById("modalDetailPenyewa").style.display = "none";
}
</script>
</body>
</html>
