<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>OutdoorKriss - Pilih Cabang</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/dashboard_penyewa.css') }}">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="brand">
        <img src="assets/images/logo.png" alt="Logo">
        OutdoorKriss
    </div>

    <div class="nav-right" id="userBtn">
        Penyewa <i class="fa-solid fa-chevron-down"></i>
    </div>

    <!-- DROPDOWN -->
    <div class="dropdown-menu" id="dropdownMenu">
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

<!-- HERO -->
<div class="hero">
    <h1>Selamat Datang Di OutdoorKriss</h1>
    <p>Penyewaan Perlengkapan terpercaya di Banyuwangi</p>
    <small>Silakan pilih cabang di bawah ini untuk melanjutkan.</small>
</div>

<div class="container">
    <div class="grid">
     @if($adminpusat)
    <a href="{{ route('pilih.pusat', $adminpusat->idusers) }}" class="card pusat">
        <div class="card-title">{{ $adminpusat->nama }}</div>

        <div class="info">
            <i class="fa-solid fa-location-dot"></i>
            {{ $adminpusat->alamat }}
        </div>

        <div class="info">
            <i class="fa-brands fa-whatsapp"></i>
            {{ $adminpusat->no_telepon }}
        </div>
    </a>
@endif

        @foreach ($cabang as $c)
            @if ($c->status_cabang === 'aktif')
                <a href="{{ route('pilih.cabang', $c->idcabang) }}" class="card">
                    <div class="card-title">{{ $c->nama_cabang }}</div>

                    <div class="info">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $c->lokasi }}
                    </div>

                    <div class="info">
                        <i class="fa-brands fa-whatsapp"></i>
                        {{ $c->no_telepon ?? '082331077579' }}
                    </div>
                </a>
            @endif
        @endforeach
    </div>
</div>



    <!-- PAGINATION -->
    <div class="pagination">
        <span>&laquo;</span>
        <span class="active">1</span>
        <span>2</span>
        <span>3</span>
        <span>4</span>
        <span>&raquo;</span>
    </div>
</div>

<script>
const userBtn = document.getElementById('userBtn');
const dropdownMenu = document.getElementById('dropdownMenu');

userBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    dropdownMenu.classList.toggle('show');
});

document.addEventListener('click', function () {
    dropdownMenu.classList.remove('show');
});
</script>

</body>
</html>
