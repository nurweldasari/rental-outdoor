<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('assets/images/logo1.png') }}" type="image/png">
<link rel="stylesheet" href="{{ asset('css/dashboard_cabang.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body>
<div class="layout">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="logo">
      <img src="{{ asset('assets/images/logo.png') }}" class="logo-img">
    </div>
    <button class="toggle-btn" id="toggleBtn">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>

  <nav>
    {{-- ================= DASHBOARD ================= --}}
<a href="{{ route('dashboard') }}"
   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge"></i>
    <span>Dashboard</span>
</a>


{{-- ================================================= --}}
{{-- ================= ADMIN CABANG ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'admin_cabang')

    <a>
        <i class="fa-solid fa-file-contract"></i>
        <span>Kontrak Franchise</span>
    </a>

    <div class="menu-title">
        Manajemen Penyewa <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a href="/data_penyewa">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>

    <div class="menu-title">
        Manajemen Penyewaan <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a><i class="fa-solid fa-cart-shopping"></i><span>Penyewaan</span></a>
    <a><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat Penyewaan</span></a>
    <a>
        <i class="fa-solid fa-print"></i>
        <span>Laporan Pendapatan</span>
    </a>

    <div class="menu-title">
        Manajemen Alat <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a><i class="fa-solid fa-store"></i><span>Data Alat</span></a>
    <a><i class="fa-solid fa-layer-group"></i><span>Data Kategori</span></a>

    <a>
        <i class="fa-solid fa-sack-dollar"></i>
        <span>Bagi Hasil</span>
    </a>

@endif


{{-- ================================================= --}}
{{-- ================= ADMIN PUSAT ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'admin_pusat')

    <div class="menu-title">
        Manajemen Penyewa <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a href="/data_penyewa">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>

    <div class="menu-title">
        Manajemen Penyewaan <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a><i class="fa-solid fa-cart-shopping"></i><span>Penyewaan</span></a>
    <a><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat Penyewaan</span></a>

    <a>
        <i class="fa-solid fa-file-lines"></i>
        <span>Laporan Pendataan</span>
    </a>

    <div class="menu-title">
        Manajemen Alat <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a><i class="fa-solid fa-store"></i><span>Data Alat</span></a>
    <a><i class="fa-solid fa-layer-group"></i><span>Data Kategori</span></a>

@endif


{{-- ================================================= --}}
{{-- ================= OWNER ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'owner')

    <div class="menu-title">
        Manajemen Penyewa <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a href="/data_penyewa">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>

    <div class="menu-title">
        Manajemen Penyewaan <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a><i class="fa-solid fa-cart-shopping"></i><span>Penyewaan</span></a>
    <a><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat Penyewaan</span></a>

    <a>
        <i class="fa-solid fa-file-lines"></i>
        <span>Laporan Pendataan</span>
    </a>

    <div class="menu-title">
        Manajemen Alat <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a><i class="fa-solid fa-store"></i><span>Data Alat</span></a>
    <a><i class="fa-solid fa-layer-group"></i><span>Data Kategori</span></a>

    <a>
        <i class="fa-solid fa-truck-fast"></i>
        <span>Distribusi Produk</span>
    </a>

    <div class="menu-title">
        Manajemen Cabang <i class="fa-solid fa-chevron-down"></i>
    </div>
    <a href="/cabang">
        <i class="fa-solid fa-store"></i>
        <span>Data Cabang</span>
    </a>
    <a href="/laporan-cabang">
        <i class="fa-solid fa-chart-line"></i>
        <span>Laporan Cabang</span>
    </a>

    <a>
        <i class="fa-solid fa-sack-dollar"></i>
        <span>Bagi Hasil</span>
    </a>

@endif

</aside>

<main class="main">

<header class="topbar">
  <h2>Dashboard</h2>

  <div class="user-dropdown">
  <button class="user-btn" id="userBtn">
    <span>
      {{ ucwords(str_replace('_', ' ', auth()->user()->status)) }}
    </span>
    <i class="fa-solid fa-chevron-down"></i>
  </button>


    <div class="dropdown-menu" id="dropdownMenu">
      <a href="/profil_cabang">
        <i class="fa-solid fa-gear"></i>
        <span>Pengaturan Akun</span>
      </a>

      <hr>

      <form method="POST" action="{{ route('logout') }}" style="margin:0">
        @csrf
        <a href="#" class="logout"
           onclick="event.preventDefault(); this.closest('form').submit();">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span>Logout</span>
        </a>
      </form>
    </div>
  </div>
</header>

<div class="content-wrapper">

  <div class="filter">
    <select>
      <option>Pilih Tahun</option>
      <option>2024</option>
      <option>2025</option>
    </select>
  </div>

  <section class="cards">
    <div class="card">
      <i class="fa-solid fa-user"></i>
      <h2>250</h2>
      <p>Total Penyewa</p>
    </div>

    <div class="card">
      <i class="fa-solid fa-cart-shopping"></i>
      <h2>50</h2>
      <p>Total Penyewaan</p>
    </div>

    <div class="card">
    <i class="fa-solid fa-store"></i>
      <h2>150</h2>
      <p>Total Alat</p>
    </div>

    <div class="card">
      <i class="fa-solid fa-layer-group"></i>
      <h2>15</h2>
      <p>Kategori</p>
    </div>
  </section>

</div>
</main>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');
toggleBtn.onclick = () => sidebar.classList.toggle('collapsed');

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

userBtn.addEventListener('click', e => {
  e.stopPropagation();
  dropdownMenu.classList.toggle('show');
});

document.addEventListener('click', () => {
  dropdownMenu.classList.remove('show');
});
</script>

</body>
</html>
