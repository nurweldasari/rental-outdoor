@extends('layouts.landing')

@section('title','Home')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
@endpush
@section('content')

<!-- HERO -->
<section class="hero-penyewa">
  <div class="overlay-penyewa"></div>

  <div class="hero-content-penyewa">
    <h1>Sewa Perlengkapan Outdoor, Lebih Mudah dan Terpercaya!!</h1>
    <p>
      Berbagai perlengkapan outdoor siap disewa sesuai kebutuhan petualanganmu,
      lengkap dan berkualitas di setiap cabang.
    </p>

    <div class="hero-btn-penyewa">
      <a href="{{ route('register.penyewa.form') }}" class="btn-white">Daftar Sekarang</a>
      <a href="#cabang" class="btn-orange">Lihat Cabang</a>
    </div>
  </div>
</section>

<!-- PROFIL -->
<section class="profil" id="profil">
  <div class="profil-wrap">
    <div class="profil-img"></div>

    <div class="profil-text">
      <h2>PROFIL</h2>
      <div class="line-title"></div>

      <p>
        OutdoorKriss penyedia jasa penyewaan perlengkapan outdoor terpercaya yang menyediakan berbagai pilihan peralatan berkualitas untuk mendukung aktivitas petualangan Anda.
        Kantor pusat OutdoorKriss beralamat di Dusun Krajan I, Kabupaten Banyuwangi.
        Usaha ini didirikan pada tahun 2017 dan terus berkembang hingga memiliki beberapa cabang.
      </p>
    </div>
  </div>
</section>

<!-- CABANG -->
<section class="cabang" id="cabang">

  <h2>DAFTAR CABANG</h2>
  <div class="line-title"></div>

  <div class="cabang-container">

    <!-- ARROW LEFT -->
    @if ($cabang->onFirstPage())
        <button class="nav-arrow left" disabled>
            <i class="fas fa-chevron-left"></i>
        </button>
    @else
        <a href="{{ $cabang->previousPageUrl() }}" class="nav-arrow left">
            <i class="fas fa-chevron-left"></i>
        </a>
    @endif

    <!-- GRID -->
    <div class="cabang-grid">

      @foreach($cabang as $c)
      <div class="card">

          <div class="icon">
              <i class="fas fa-map-marker-alt"></i>
          </div>

          <div class="text">

    <h4>{{ $c->nama_cabang }}</h4>

    <p>
        {{ $c->lokasi }}
    </p>

</div>

      </div>
      @endforeach

    </div>

    <!-- ARROW RIGHT -->
    @if ($cabang->hasMorePages())
        <a href="{{ $cabang->nextPageUrl() }}" class="nav-arrow right">
            <i class="fas fa-chevron-right"></i>
        </a>
    @else
        <button class="nav-arrow right" disabled>
            <i class="fas fa-chevron-right"></i>
        </button>
    @endif

  </div>

</section>

<!-- SYARAT -->
<section class="syarat" id="syarat">

  <h2>Syarat dan Ketentuan Penyewaan</h2>
  <div class="line-title"></div>

  <div class="timeline">

    <div class="timeline-line"></div>

    <div class="timeline-row">
      <div class="timeline-box left">
        <b>Identitas Penyewa</b>
        <p>Menyerahkan identitas berupa KTP</p>
      </div>
      <div class="timeline-circle">1</div>
      <div></div>
    </div>

    <div class="timeline-row">
      <div></div>
      <div class="timeline-circle">2</div>
      <div class="timeline-box right">
        <b>Denda Pengembalian</b>
        <p>Keterlambatan dikenakan denda per hari</p>
      </div>
    </div>

    <div class="timeline-row">
      <div class="timeline-box left">
        <b>Kerusakan atau Kehilangan</b>
        <p>Menjadi tanggung jawab penyewa</p>
      </div>
      <div class="timeline-circle">3</div>
      <div></div>
    </div>

    <div class="timeline-row">
      <div></div>
      <div class="timeline-circle">4</div>
      <div class="timeline-box right">
        <b>Jam Operasional</b>
        <p>08.00 - 19.00 WIB</p>
      </div>
    </div>

  </div>
</section>

@endsection