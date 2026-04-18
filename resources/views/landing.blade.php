<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OutdoorKriss</title>

  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
  <div class="nav-left">
    <img src="assets/images/logo.png" class="logo-img">
    <span class="logo-text">OutdoorKriss</span>
  </div>

  <nav class="nav-menu">
    <a class="active">Home</a>
    <a>Profil</a>
    <a>Syarat</a>
    <a>Cabang</a>
    <a>Franchise</a>
  </nav>

 <a href="{{ route('login') }}" class="btn-login">Login</a>
</header>

<!-- HERO -->
<section class="hero">
  <div class="overlay"></div>

  <div class="hero-content">
    <h1>Sewa Perlengkapan Outdoor, Lebih Mudah dan Terpercaya!!</h1>
    <p>
      Berbagai perlengkapan outdoor siap disewa sesuai kebutuhan petualanganmu,
      lengkap dan berkualitas di setiap cabang.
    </p>

    <div class="hero-btn">
      <button class="btn-white">Daftar Sekarang</button>
      <button class="btn-orange">Lihat Cabang</button>
    </div>
  </div>
</section>

<!-- PROFIL -->
<section class="profil">
  <div class="container profil-wrap">
    <div class="profil-img"></div>

    <div class="profil-text">
      <h2>PROFIL</h2>
      <div class="line-title"></div>
      <p>
        OutdoorKriss penyedia jasa penyewaan perlengkapan outdoor terpercaya yang menyediakan berbagai pilihan peralatan berkualitas untuk mendukung aktivitas petualangan Anda.
        Kantor pusat OutdoorKriss yang beralamat di Dusun Krajan I, Kabupaten Banyuwangi.
        Usaha ini didirikan pada tahun 2017 dan terus berkembang hingga memiliki beberapa cabang.
      </p>
    </div>
  </div>
</section>

<!-- CABANG -->
<!-- CABANG -->
<section class="cabang">
  <h2>DAFTAR CABANG</h2>
  <div class="title-line"></div>

  <div class="cabang-container">
    <button class="nav-arrow left"><i class="fas fa-chevron-left"></i></button>

    <div class="cabang-grid">
      <!-- CARD -->
      <div class="card">
        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
        <div class="text">
          <h4>OutdoorKriss Rogojampi</h4>
          <p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p>
        </div>
      </div>

      <!-- DUPLIKASI SAMA -->
      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>
      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>

      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>
      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>
      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>

      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>
      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>
      <div class="card"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="text"><h4>OutdoorKriss Rogojampi</h4><p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Kecamatan Tegalsari, Kabupaten Banyuwangi</p></div></div>
    </div>

    <button class="nav-arrow right"><i class="fas fa-chevron-right"></i></button>
  </div>
</section>

<!-- SYARAT -->
<section class="syarat">
  <h2>Syarat dan Ketentuan Penyewaan</h2>
  <div class="title-line dark"></div>

  <div class="timeline">

    <div class="timeline-line"></div>

    <!-- 1 -->
    <div class="timeline-row">
      <div class="timeline-box left">
        <b>Identitas Penyewa</b>
        <p>Menyerahkan identitas berupa KTP</p>
      </div>

      <div class="timeline-circle">1</div>

      <div class="timeline-empty"></div>
    </div>

    <!-- 2 -->
    <div class="timeline-row">
      <div class="timeline-empty"></div>

      <div class="timeline-circle">2</div>

      <div class="timeline-box right">
        <b>Denda Pengembalian</b>
        <p>Keterlambatan pengembalian dikenakan denda per hari</p>
      </div>
    </div>

    <!-- 3 -->
    <div class="timeline-row">
      <div class="timeline-box left">
        <b>Kerusakan atau Kehilangan</b>
        <p>Menjadi tanggung jawab penyewa dan dikenakan biaya</p>
      </div>

      <div class="timeline-circle">3</div>

      <div class="timeline-empty"></div>
    </div>

    <!-- 4 -->
    <div class="timeline-row">
      <div class="timeline-empty"></div>

      <div class="timeline-circle">4</div>

      <div class="timeline-box right">
        <b>Jam Operasional</b>
        <p>08.00 - 19.00 WIB</p>
      </div>
    </div>

  </div>
</section>

<footer class="footer">
  <div class="footer-container">

    <div class="footer-left">
      <img src="assets/images/logo.png">
      <p>Dusun Krajan I RT 07/RW 01, Desa Tegalsari, Banyuwangi</p>
    </div>

    <div class="footer-middle">
      <p><i class="fab fa-whatsapp"></i> 082331077579</p>
      <p><i class="fab fa-instagram"></i> @outdoorkriss.store</p>
    </div>

    <div class="footer-right">
      <p><i class="fab fa-facebook"></i> Outdoorkriss</p>
      <p><i class="fab fa-tiktok"></i> @outdoorkriss.store</p>
    </div>

  </div>
</footer>

</body>
</html>