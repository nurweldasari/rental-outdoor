<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OutdoorRent - Sistem Penyewaan Perlengkapan Outdoor</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; color: #222; background: #fff; }
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
    .logo { font-weight: 700; font-size: 22px; color: #2e7d32; }
    nav a { margin: 0 16px; text-decoration: none; color: #555; font-weight: 500; }
    nav a:hover { color: #2e7d32; }
    .actions { display: flex; align-items: center; gap: 12px; }
    .btn-login { padding: 10px 20px; border-radius: 30px; border: 1px solid #2e7d32; background: #fff; color: #2e7d32; cursor: pointer; }
    .btn-login:hover { background: #2e7d32; color: #fff; }

    .hero { display: grid; grid-template-columns: 1.1fr 1fr; align-items: center; padding: 80px 60px; }
    .hero h1 { font-size: 52px; line-height: 1.2; margin-bottom: 20px; }
    .hero p { color: #666; max-width: 520px; margin-bottom: 32px; }
    .hero-actions button { padding: 14px 28px; border-radius: 30px; border: none; font-size: 15px; cursor: pointer; margin-right: 14px; }
    .primary { background: #2e7d32; color: #fff; }
    .secondary { background: transparent; border: 1px solid #2e7d32; color: #2e7d32; }

    .section { padding: 80px 60px; }
    .section h2 { font-size: 36px; margin-bottom: 20px; }
    .section p { max-width: 800px; color: #555; }

    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap: 24px; margin-top: 40px; }
    .card { border: 1px solid #eee; border-radius: 16px; padding: 24px; box-shadow: 0 4px 10px rgba(0,0,0,.05); }
    .card h3 { margin-bottom: 10px; }

    footer { background: #1b1b1b; color: #eee; padding: 40px 60px; }
    footer .footer-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 24px; }
    footer h4 { margin-bottom: 14px; }
    footer p, footer a { font-size: 14px; color: #ccc; text-decoration: none; }

    /* Modal Login */
    .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); align-items: center; justify-content: center; }
    .modal-content { background: #fff; padding: 30px; border-radius: 16px; width: 360px; }
    .modal-content h3 { margin-bottom: 20px; }
    .branch-list button { width: 100%; padding: 12px; margin-bottom: 10px; border-radius: 10px; border: 1px solid #2e7d32; background: #fff; cursor: pointer; }
    .branch-list button:hover { background: #2e7d32; color: #fff; }
  </style>
</head>
<body>

<header>
  <div class="logo">OutdoorRent</div>
  <nav>
    <a href="#home">Home</a>
    <a href="#profil">Profil</a>
    <a href="#syarat">Syarat</a>
    <a href="#cabang">Daftar Cabang</a>
    <a href="#katalog">Katalog</a>
  </nav>
  <div class="actions">
    <button class="btn-login" onclick="openModal()">Login</button>
  </div>
</header>

<section class="hero" id="home">
  <div>
    <h1>Sistem Penyewaan<br>Perlengkapan Outdoor</h1>
    <p>Platform terintegrasi untuk pengelolaan dan penyewaan perlengkapan outdoor seperti tenda, carrier, alat camping, dan lainnya dengan sistem cabang yang terpusat.</p>
    <div class="hero-actions">
      <button class="primary">Lihat Katalog</button>
      <button class="secondary">Daftar Cabang</button>
    </div>
  </div>
  <div>
    <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee" alt="Outdoor" style="width:100%; border-radius:24px;">
  </div>
</section>

<section class="section" id="profil">
  <h2>Profil Sistem</h2>
  <p>Sistem ini dirancang untuk membantu pengelolaan penyewaan perlengkapan outdoor berbasis web dengan fitur manajemen cabang, katalog alat, transaksi penyewaan, dan laporan yang terintegrasi.</p>
</section>

<section class="section" id="syarat">
  <h2>Syarat & Ketentuan</h2>
  <div class="grid">
    <div class="card"><h3>Identitas</h3><p>Penyewa wajib memiliki identitas resmi yang masih berlaku.</p></div>
    <div class="card"><h3>Durasi Sewa</h3><p>Durasi penyewaan disesuaikan dengan kesepakatan dan ketersediaan alat.</p></div>
    <div class="card"><h3>Tanggung Jawab</h3><p>Penyewa bertanggung jawab atas kerusakan atau kehilangan alat.</p></div>
  </div>
</section>

<section class="section" id="cabang">
  <h2>Daftar Cabang</h2>
  <div class="grid">
    <div class="card"><h3>Cabang Jakarta</h3><p>Jl. Raya Outdoor No.1</p></div>
    <div class="card"><h3>Cabang Bandung</h3><p>Jl. Pegunungan No.12</p></div>
    <div class="card"><h3>Cabang Yogyakarta</h3><p>Jl. Petualang No.7</p></div>
  </div>
</section>

<section class="section" id="katalog">
  <h2>Katalog Perlengkapan</h2>
  <div class="grid">
    <div class="card"><h3>Tenda Dome</h3><p>Kapasitas 4 orang</p></div>
    <div class="card"><h3>Carrier 60L</h3><p>Nyaman untuk pendakian</p></div>
    <div class="card"><h3>Kompor Portable</h3><p>Ringan dan praktis</p></div>
  </div>
</section>

<footer>
  <div class="footer-grid">
    <div>
      <h4>OutdoorRent</h4>
      <p>Sistem penyewaan perlengkapan outdoor berbasis cabang.</p>
    </div>
    <div>
      <h4>Menu</h4>
      <a href="#home">Home</a><br>
      <a href="#katalog">Katalog</a><br>
      <a href="#cabang">Cabang</a>
    </div>
    <div>
      <h4>Kontak</h4>
      <p>Email: info@outdoorrent.id</p>
      <p>Telp: 0812-3456-7890</p>
    </div>
  </div>
</footer>

<div class="modal" id="loginModal">
  <div class="modal-content">
    <h3>Pilih Cabang Login</h3>
    <div class="branch-list">
      <button>Cabang Jakarta</button>
      <button>Cabang Bandung</button>
      <button>Cabang Yogyakarta</button>
    </div>
    <button class="btn-login" style="width:100%" onclick="closeModal()">Tutup</button>
  </div>
</div>

<script>
  function openModal() {
    document.getElementById('loginModal').style.display = 'flex';
  }
  function closeModal() {
    document.getElementById('loginModal').style.display = 'none';
  }
</script>

</body>
</html>