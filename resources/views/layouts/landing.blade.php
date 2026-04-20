<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/landing_page_cabang.css') }}">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar">
    
    <!-- KIRI -->
    <div class="nav-left">
        <img src="{{ asset('assets/images/logo.png') }}" class="logo-img">
        <span class="brand">OutdoorKriss</span>
    </div>

    <!-- KANAN -->
    <div class="nav-right">
        <ul>
            <li><a href="/" class="{{ request()->path() == '/' ? 'active' : '' }}">Home</a></li>
            <li><a href="#">Profil</a></li>
            <li><a href="#">Syarat</a></li>
            <li><a href="#">Cabang</a></li>
            <li>
                <a href="/landing_page_cabang"
                   class="{{ request()->is('landing_page_cabang') ? 'active' : '' }}">
                   Franchise
                </a>
            </li>
        </ul>

        <a href="/login" class="btn-login">Login</a>
    </div>

</nav>

<!-- ================= CONTENT ================= -->
@yield('content')
<!-- ================= FOOTER ================= -->
<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="footer-container">

        <!-- KIRI -->
        <div class="footer-left">
            <div class="footer-logo">
                <img src="{{ asset('assets/images/logo.png') }}" class="footer-logo-img">
            <p class="footer-address">
                Dusun Krajan I, RT 07/RW 01, Desa Tegalsari, <br>
                Kecamatan Tegalsari, Kabupaten Banyuwangi. 
            </p>
            </div>
        </div>

        <!-- KANAN -->
        <div class="footer-right">

            <!-- BARIS ATAS -->
            <div class="footer-row">
                <a href="https://wa.me/6282331077579" target="_blank" class="footer-item">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>082331077579</span>
                </a>

                <a href="https://www.facebook.com/profile.php?id=100088564314345" class="footer-item">
                    <i class="fa-brands fa-facebook"></i>
                    <span>OutdoorKriss</span>
                </a>

                <a href="https://www.tiktok.com/@outdoorkriss.store?_r=1&_t=ZS-95fhgTCetQD" class="footer-item">
                    <i class="fa-brands fa-tiktok"></i>
                    <span>@outdoorkriss.store</span>
                </a>
            </div>

            <!-- BARIS BAWAH -->
            <div class="footer-row">
                <a href="https://www.instagram.com/outdoorkriss.store" target="_blank" class="footer-item">
                    <i class="fa-brands fa-instagram"></i>
                    <span>@outdoorkriss.store</span>
                </a>

                <a href="https://youtube.com/@outdoorkrissstore?si=d0dLB8BEjDswZoZn" class="footer-item">
                    <i class="fa-brands fa-youtube"></i>
                    <span>@outdoorkrissstore</span>
                </a>
            </div>

        </div>

    </div>
</footer>
</body>
</html>