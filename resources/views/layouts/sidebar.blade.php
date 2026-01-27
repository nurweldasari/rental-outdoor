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
   class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">
    <i class="fa-solid fa-gauge"></i>
    <span>Dashboard</span>
</a>

{{-- ================================================= --}}
{{-- ================= PENYEWA ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'penyewa')

    <a href="{{ route('katalog_produk') }}"
       class="{{ ($active ?? '') === 'katalog' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Katalog Produk</span>
    </a>

    <a href="{{ route('penyewa.sewa') }}"
       class="{{ ($active ?? '') === 'sewa_penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Penyewaan Saya</span>
    </a>

    <a href="{{ route('penyewa.riwayat') }}"
       class="{{ ($active ?? '') === 'riwayat_penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Riwayat Penyewaan</span>
    </a>

@endif

{{-- ================================================= --}}
{{-- ================= ADMIN CABANG ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'admin_cabang')

    <a class="{{ ($active ?? '') === 'kontrak' ? 'active' : '' }}">
        <i class="fa-solid fa-file-contract"></i>
        <span>Kontrak Franchise</span>
    </a>

    <div class="menu-title {{ in_array($active ?? '', ['penyewa']) ? 'open' : '' }}">
        Manajemen Penyewa
    </div>

    <a href="/data_penyewa"
       class="{{ ($active ?? '') === 'penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['sewa','riwayat']) ? 'open' : '' }}">
        Manajemen Penyewaan
    </div>

    <a class="{{ ($active ?? '') === 'sewa' ? 'active' : '' }}">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Penyewaan</span>
    </a>

    <a class="{{ ($active ?? '') === 'riwayat' ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Riwayat Penyewaan</span>
    </a>

    <a class="{{ ($active ?? '') === 'laporan_pendapatan' ? 'active' : '' }}">
        <i class="fa-solid fa-print"></i>
        <span>Laporan Pendapatan</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['alat','kategori']) ? 'open' : '' }}">
        Manajemen Alat
    </div>

    <a class="{{ ($active ?? '') === 'alat' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Data Alat</span>
    </a>

    <a class="{{ ($active ?? '') === 'kategori' ? 'active' : '' }}">
        <i class="fa-solid fa-layer-group"></i>
        <span>Data Kategori</span>
    </a>

    <a class="{{ ($active ?? '') === 'bagi_hasil' ? 'active' : '' }}">
        <i class="fa-solid fa-sack-dollar"></i>
        <span>Bagi Hasil</span>
    </a>

@endif


{{-- ================================================= --}}
{{-- ================= ADMIN PUSAT ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'admin_pusat')

    <div class="menu-title {{ ($active ?? '') === 'penyewa' ? 'open' : '' }}">
        Manajemen Penyewa
    </div>

    <a href="/data_penyewa"
       class="{{ ($active ?? '') === 'penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['sewa','riwayat']) ? 'open' : '' }}">
        Manajemen Penyewaan
    </div>

    <a class="{{ ($active ?? '') === 'sewa' ? 'active' : '' }}">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Penyewaan</span>
    </a>

    <a class="{{ ($active ?? '') === 'riwayat' ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Riwayat Penyewaan</span>
    </a>

    <a class="{{ ($active ?? '') === 'laporan_pendataan' ? 'active' : '' }}">
        <i class="fa-solid fa-file-lines"></i>
        <span>Laporan Pendataan</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['alat','kategori']) ? 'open' : '' }}">
        Manajemen Alat
    </div>

    <a class="{{ ($active ?? '') === 'alat' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Data Alat</span>
    </a>

    <a class="{{ ($active ?? '') === 'kategori' ? 'active' : '' }}">
        <i class="fa-solid fa-layer-group"></i>
        <span>Data Kategori</span>
    </a>

@endif


{{-- ================================================= --}}
{{-- ================= OWNER ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'owner')

    <div class="menu-title {{ ($active ?? '') === 'penyewa' ? 'open' : '' }}">
        Manajemen Penyewa
    </div>

    <a href="/data_penyewa"
       class="{{ ($active ?? '') === 'penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['sewa','riwayat']) ? 'open' : '' }}">
        Manajemen Penyewaan
    </div>

    <a class="{{ ($active ?? '') === 'sewa' ? 'active' : '' }}">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Penyewaan</span>
    </a>

    <a class="{{ ($active ?? '') === 'riwayat' ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Riwayat Penyewaan</span>
    </a>

    <a class="{{ ($active ?? '') === 'laporan_pendataan' ? 'active' : '' }}">
        <i class="fa-solid fa-file-lines"></i>
        <span>Laporan Pendataan</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['alat','kategori','distribusi']) ? 'open' : '' }}">
        Manajemen Alat
    </div>

    <a class="{{ ($active ?? '') === 'alat' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Data Alat</span>
    </a>

    <a class="{{ ($active ?? '') === 'kategori' ? 'active' : '' }}">
        <i class="fa-solid fa-layer-group"></i>
        <span>Data Kategori</span>
    </a>

    <a class="{{ ($active ?? '') === 'distribusi' ? 'active' : '' }}">
        <i class="fa-solid fa-truck-fast"></i>
        <span>Distribusi Produk</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['cabang','laporan_cabang']) ? 'open' : '' }}">
        Manajemen Cabang
    </div>

    <a href="/cabang"
       class="{{ ($active ?? '') === 'cabang' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Data Cabang</span>
    </a>

    <a href="/laporan-cabang"
       class="{{ ($active ?? '') === 'laporan_cabang' ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line"></i>
        <span>Laporan Cabang</span>
    </a>

    <a class="{{ ($active ?? '') === 'bagi_hasil' ? 'active' : '' }}">
        <i class="fa-solid fa-sack-dollar"></i>
        <span>Bagi Hasil</span>
    </a>

@endif

</nav>
</aside>
