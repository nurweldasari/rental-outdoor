<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Cabang</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('assets/images/logo1.png') }}" type="image/png">
<link rel="stylesheet" href="{{ asset('css/profil_cabang.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    <a href="dashboard"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a>
    <a><i class="fa-solid fa-file-contract"></i><span>Kontrak Franchise</span></a>

    <div class="menu-title">Manajemen Penyewa<i class="fa-solid fa-chevron-down"></i></div>
    <a href="data_penyewa"><i class="fa-solid fa-circle-user"></i><span>Data Penyewa</span></a>

    <div class="menu-title">Manajemen Penyewaan<i class="fa-solid fa-chevron-down"></i></div>
    <a><i class="fa-solid fa-cart-shopping"></i><span>Penyewaan</span></a>
    <a><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat Penyewaan</span></a>
    <a><i class="fa-solid fa-print"></i><span>Laporan Pendapatan</span></a>

    <div class="menu-title">Manajemen Alat<i class="fa-solid fa-chevron-down"></i></div>
    <a><i class="fa-solid fa-store"></i><span>Data Alat</span></a>
    <a><i class="fa-solid fa-layer-group"></i><span>Data Kategori</span></a>
    <a><i class="fa-solid fa-sack-dollar"></i><span>Bagi Hasil</span></a>
  </nav>
</aside>

<!-- MAIN -->
<main class="main">

  <!-- NAVBAR -->
  <header class="topbar">
    <h2>Profile</h2>

    <div class="user-dropdown">
      <button class="user-btn" id="userBtn">
        <span>Admin Cabang</span>
        <i class="fa-solid fa-chevron-down"></i>
      </button>

      <div class="dropdown-menu" id="dropdownMenu">

    <form method="POST" action="{{ route('logout') }}" style="margin:0">
      @csrf
      <a href="#" class="logout"
         onclick="event.preventDefault(); this.closest('form').submit();">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
      </a>
    </form>
    </div>
  </header>
  <!-- END NAVBAR -->

  <!-- CONTENT -->
  <div class="content-wrapper">

    <div class="profile-container">

      <!-- TAB MENU -->
      <div class="profile-tabs">
        <div class="tab active">
          <i class="fa-solid fa-user"></i>
          <span>Profile</span>
        </div>

         <a href="/ganti_password" class="tab">
    <i class="fa-solid fa-lock"></i>
    Ganti Password
</a>

        <div class="tab">
          <i class="fa-solid fa-money-check"></i>
          <span>Rekening</span>
        </div>
      </div>

  <!-- FORM PROFILE CABANG -->
  <form
      class="profile-form"
      method="POST"
      action="{{ route('profil.cabang.update') }}"
  >
    @csrf

    <!-- DATA CABANG -->
    <div class="form-row">
      <input
        type="text"
        name="nama_cabang"
        placeholder="Nama Cabang"
        value="{{ old('nama_cabang', $cabang->nama_cabang ?? '') }}"
        required
      >

      <input
        type="text"
        name="lokasi"
        placeholder="Lokasi Cabang"
        value="{{ old('lokasi', $cabang->lokasi ?? '') }}"
        required
      >
    </div>

    <!-- DATA ADMIN CABANG -->
    <div class="form-row">
      <input
        type="text"
        name="nama"
        placeholder="Nama Admin Cabang"
        value="{{ old('nama', $user->nama) }}"
        required
      >

      <input
        type="text"
        name="no_telepon"
        placeholder="No. Telephone Admin Cabang"
        value="{{ old('no_telepon', $user->no_telepon) }}"
        required
      >
    </div>

    <div class="form-row full">
      <input
        type="text"
        name="alamat"
        placeholder="Alamat Domisili Admin Cabang"
        value="{{ old('alamat', $user->alamat) }}"
        required
      >
    </div>

    <div class="form-row center">
      <input
        type="text"
        name="username"
        placeholder="Username"
        value="{{ old('username', $user->username) }}"
        required
      >
    </div>

    <button type="submit" class="btn-simpan">
      Simpan
    </button>

  </form>

</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');

/* COLLAPSE */
toggleBtn.onclick = () => sidebar.classList.toggle('collapsed');

/* DROPDOWN */
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: "{{ session('success') }}",
    showConfirmButton: false,
    timer: 2000
});
</script>
@endif

</body>
</html>
