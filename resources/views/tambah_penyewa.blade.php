<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Penyewa</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('assets/images/logo1.png') }}" type="image/png">
<link rel="stylesheet" href="{{ asset('css/tambah_penyewa.css') }}">
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
    <a href="dashboard_cabang"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a>
    <a><i class="fa-solid fa-file-contract"></i><span>Kontrak Franchise</span></a>

    <div class="menu-title">Manajemen Penyewa<i class="fa-solid fa-chevron-down"></i></div>
    <a class="active"><i class="fa-solid fa-circle-user"></i><span>Data Penyewa</span></a>

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
      <h2>Data Penyewa</h2>
      <div class="user-dropdown">
  <button class="user-btn" id="userBtn">
    <span>Admin Cabang</span>
    <i class="fa-solid fa-chevron-down"></i>
  </button>

  <div class="dropdown-menu" id="dropdownMenu">
    <a href="/profil_cabang"">
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

        <span id="uploadText" class="upload-text">
          Upload gambar identitas
        </span>

        <input 
          type="file"
          name="gambar_identitas"
          id="gambar"
          accept="image/*"
          hidden
          onchange="showFileName(this)">
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

function showFileName(input) {
  if (input.files && input.files.length > 0) {
    document.getElementById('uploadText').innerText =
      input.files[0].name;
  }

  document.querySelector('.upload-box')
    .classList.add('file-selected');
}

</script>
</body>
</html>