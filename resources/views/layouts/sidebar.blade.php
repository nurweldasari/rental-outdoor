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

    <a href="{{ route('item_penyewaan') }}"
       class="{{ ($active ?? '') === 'penyewaan' ? 'active' : '' }}">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Penyewaan</span>
    </a>

    <a href="{{ route('riwayat_penyewaan') }}"
       class="{{ ($active ?? '') === 'riwayat' ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Riwayat Penyewaan</span>
    </a>

@endif

{{-- ================================================= --}}
{{-- ================= ADMIN CABANG ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'admin_cabang')

   <a href="/kontrak_franchise" 
   class="{{ ($active ?? '') === 'kontrak' ? 'active' : '' }}">
        <i class="fa-solid fa-file-contract"></i>
        <span>Kontrak Franchise</span>
    </a>

    <div class="menu-title {{ in_array($active ?? '', ['penyewa']) ? 'open' : '' }}">
        Manajemen Penyewa <i class="fa-solid fa-chevron-down"></i>
    </div>

    <a href="/data_penyewa"
       class="{{ ($active ?? '') === 'penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['sewa','riwayat']) ? 'open' : '' }}">
        Manajemen Penyewaan <i class="fa-solid fa-chevron-down"></i>
    </div>

    <a href="{{ route('data_penyewaan') }}"
       class="{{ ($active ?? '') === 'penyewaan' ? 'active' : '' }}">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Penyewaan</span>
    </a>

    <a href="{{ route('data_riwayat') }}"
       class="{{ ($active ?? '') === 'riwayat' ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Riwayat Penyewaan</span>
    </a>

    <a href="{{ route('laporan') }}"
       class="{{ ($active ?? '') === 'laporan' ? 'active' : '' }}">
        <i class="fa-solid fa-print"></i>
        <span>Laporan Pendapatan</span>
    </a>
    
    <div class="menu-title {{ in_array($active ?? '', ['produk','kategori']) ? 'open' : '' }}">
        Manajemen Produk <i class="fa-solid fa-chevron-down"></i>
    </div>


    <a href="{{ route('produk_cabang') }}"
    class="{{ ($active ?? '') === 'produk.cabang' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Data Produk</span>
    </a>

    <a href="{{ route('data_permintaan') }}"
    class="{{ ($active ?? '') === 'data.permintaan' ? 'active' : '' }}">
        <i class="fa-solid fa-box"></i>
        <span>Data Permintaan</span>
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
        Manajemen Penyewa <i class="fa-solid fa-chevron-down"></i>
    </div>

    <a href="/data_penyewa"
       class="{{ ($active ?? '') === 'penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['sewa','riwayat']) ? 'open' : '' }}">
        Manajemen Penyewaan <i class="fa-solid fa-chevron-down"></i>
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
        <i class="fa-solid fa-print"></i>
        <span>Laporan Pendataan</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['produk','kategori']) ? 'open' : '' }}">
        Manajemen Produk <i class="fa-solid fa-chevron-down"></i>
    </div>

    <a href="{{ route('data_produk') }}"
    class="{{ ($active ?? '') === 'produk' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Data Produk</span>
    </a>

    <a href="{{ route('data_kategori') }}"
   class="{{ ($active ?? '') === 'kategori' ? 'active' : '' }}">
    <i class="fa-solid fa-layer-group"></i>
    <span>Data Kategori</span>
</a>


@endif


{{-- ================================================= --}}
{{-- ================= OWNER ================= --}}
{{-- ================================================= --}}
@if(auth()->user()->status === 'owner')

    <div class="menu-title {{ ($active ?? '') === 'penyewa' ? 'open' : '' }}">
        Manajemen Penyewa <i class="fa-solid fa-chevron-down"></i>
    </div>

    <a href="/data_penyewa"
       class="{{ ($active ?? '') === 'penyewa' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-user"></i>
        <span>Data Penyewa</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['sewa','riwayat']) ? 'open' : '' }}">
        Manajemen Penyewaan <i class="fa-solid fa-chevron-down"></i>
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
        <i class="fa-solid fa-print"></i>
        <span>Laporan Pendataan</span>
    </a>


    <div class="menu-title {{ in_array($active ?? '', ['produk','kategori','distribusi']) ? 'open' : '' }}">
        Manajemen Produk <i class="fa-solid fa-chevron-down"></i>
    </div>

    <a href="{{ route('data_produk') }}" 
    class="{{ ($active ?? '') === 'produk' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span>Data Produk</span>
    </a>

 <a href="{{ route('data_kategori') }}"
   class="{{ ($active ?? '') === 'kategori' ? 'active' : '' }}">
    <i class="fa-solid fa-layer-group"></i>
    <span>Data Kategori</span>
</a>

    <a href="{{ route('distribusi_produk') }}"
   class="{{ ($active ?? '') === 'distribusi' ? 'active' : '' }}">
    <i class="fa-solid fa-truck"></i>
    <span>Distribusi Produk</span>
</a>


    <div class="menu-title {{ in_array($active ?? '', ['cabang','laporan_cabang']) ? 'open' : '' }}">
        Manajemen Cabang <i class="fa-solid fa-chevron-down"></i>
    </div>

    <a href="/cabang"
       class="{{ ($active ?? '') === 'cabang' ? 'active' : '' }}">
        <i class="fa-solid fa-building"></i>
        <span>Data Cabang</span>
    </a>

    <a href="/laporan-cabang"
       class="{{ ($active ?? '') === 'laporan_cabang' ? 'active' : '' }}">
        <i class="fa-solid fa-print"></i>
        <span>Laporan Cabang</span>
    </a>

    <a href="/bagi-hasil"
    class="{{ ($active ?? '') === 'bagi_hasil' ? 'active' : '' }}">
        <i class="fa-solid fa-sack-dollar"></i>
        <span>Bagi Hasil</span>
    </a>

@endif

</nav>
</aside>
