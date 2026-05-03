@extends('layouts.landing')

@section('title','Franchise')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/landing_page_cabang.css') }}">
@endpush

@section('content')

<!-- ================= HERO ================= -->
<section class="hero">
    <img src="{{ asset('assets\images\lokasipusat.jpeg') }}" class="hero-img">

    <div class="hero-overlay">
        <h1>Kemitraan Franchise OutdoorKriss</h1>
        <p>
            Kerja sama operasional penyewaan perlengkapan outdoor dengan<br> sistem terkelola dan transparan.
        </p>

        <a href="{{ route('register.admin_cabang.form') }}" class="btn-cta">Ajukan Kerja Sama</a>
        <small>Pengajuan akan direview oleh owner</small>
    </div>
</section>

<!-- ================= GAMBARAN ================= -->
<section class="gambaran" id="gambaran">
    <div class="gambaran-container">

        <h2>GAMBARAN KERJA SAMA</h2>

        <div class="title-line">
            <span></span>
        </div>

        <div class="gambaran-img">
            <img src="{{ asset('assets/images/gambar kerjasama.png') }}">
        </div>

    </div>
</section>
<!-- ================= KEUNTUNGAN ================= -->
<section class="keuntungan" id="keuntungan">
    <div class="keuntungan-container">

        <h2>KEUNTUNGAN MENJADI CABANG</h2>

<div class="keuntungan-grid">

        <div class="grid-3">
            <div class="box">
                <i class="fa-solid fa-file-contract"></i>
                <p>Kesepakatan MoU</p>
            </div>

            <div class="box">
                <i class="fa-solid fa-truck"></i>
                <p>Perlengkapan Dari Owner</p>
            </div>

            <div class="box">
                <i class="fa-solid fa-copyright"></i>
                <p>Brand Siap Pakai</p>
            </div>

            <div class="box">
                <i class="fa-solid fa-sack-dollar"></i>
                <p>Bagi Hasil Jelas</p>
            </div>

            <div class="box">
                <i class="fa-solid fa-handshake"></i>
                <p>Sistem Transparan</p>
            </div>

            <div class="box">
                <i class="fa-solid fa-campground"></i>
                <p>Fokus Penyewaan</p>
            </div>
        </div>

    </div>
</section>
<!-- ================= ALUR ================= -->
<section class="section" id="section">
    <h2>ALUR KERJA SAMA FRANCHISE</h2>

    <div class="title-line">
        <span></span>
    </div>  

    <div class="timeline">

        <div class="step left" data-step="1">
            <div class="content">
                <h4>Kesepakatan Kerja Sama (MoU Offline)</h4>
                <p>
                    Calon cabang melakukan pertemuan langsung dengan owner untuk<br> 
                    membahas skema kerja sama dan menandatangani perjanjian (MoU).
                </p>
            </div>
        </div>

        <div class="step right" data-step="2">
            <div class="content">
                <h4>Pendaftaran Cabang di Sistem</h4>
                <p>
                    Cabang mendaftarkan akun melalui website dan<br> 
                    mengunggah dokumen MoU sebagai bukti kerja sama yang sah.
                </p>
            </div>
        </div>

        <div class="step left" data-step="3">
            <div class="content">
                <h4>Pengajuan Distribusi Perlengkapan</h4>
                <p>
                    Cabang mengajukan permintaan perlengkapan awal<br> 
                    melalui sistem sesuai dengan ketentuan dalam MoU.
                </p>
            </div>
        </div>

        <div class="step right" data-step="4">
            <div class="content">
                <h4>Proses Penyewaan oleh Cabang</h4>
                <p>
                    Setelah perlengkapan diterima, cabang dapat mulai melakukan<br> 
                    aktivitas penyewaan dan pendapatan dicatat otomatis oleh sistem.
                </p>
            </div>
        </div>

        <div class="step left" data-step="5">
            <div class="content">
                <h4>Perhitungan & Bagi Hasil</h4>
                <p>
                    Sistem menghitung pendapatan dan membagi hasil secara<br> 
                    otomatis berdasarkan skema yang telah disepakati dalam MoU.
                </p>
            </div>
        </div>

    </div>
</section>
@endsection