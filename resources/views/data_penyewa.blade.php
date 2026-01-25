<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Penyewa</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('assets/images/logo1.png') }}" type="image/png">
<link rel="stylesheet" href="{{ asset('css/data_penyewa.css') }}">
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
<div class="page-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Pencarian...">
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

        <table id="dataTable">

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
        <span class="field-label">Nama</span>
        <input type="text" id="modalNama" disabled>
      </div>

      <div class="form-group">
        <span class="field-label">Username</span>
        <input type="text" id="modalUsername" disabled>
      </div>

      <div class="form-group">
        <span class="field-label">No. Telephone</span>
        <input type="text" id="modalTelepon" disabled>
      </div>

      <div class="form-group">
        <span class="field-label">Alamat</span>
        <input type="text" id="modalAlamat" disabled>
      </div>


      </div>
      <div id="imgPreviewOverlay">
  <span onclick="closeImgPreview()">&times;</span>
  <img>
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
const modalFoto = document.getElementById('modalFoto');
const overlay = document.getElementById('imgPreviewOverlay');
const overlayImg = overlay.querySelector('img');

modalFoto.addEventListener('click', () => {
  overlayImg.src = modalFoto.src;
  overlay.style.display = 'flex';
});

function closeImgPreview() {
  overlay.style.display = 'none';
}

document.getElementById('searchInput').addEventListener('keyup', function () {
  let value = this.value.toLowerCase();
  let rows = document.querySelectorAll('#dataTable tbody tr');

  rows.forEach(row => {
    let text = row.innerText.toLowerCase();
    row.style.display = text.includes(value) ? '' : 'none';
  });
});
</script>
</body>
</html>
