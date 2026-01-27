@extends('layouts.app')

@php
    $active = 'produk.cabang';
@endphp

@section('title','Data Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/produk_cabang.css') }}">
@endpush

@section('content')

<div class="page-container">

    <!-- ================= DATA PRODUK ================= -->
    <div class="section data-produk">
        <div class="produk-header">
            <form method="GET">
                <input type="text" name="search" class="search-input" placeholder="Pencarian..." value="{{ request('search') }}">
                <div class="produk-actions">
                    <a href="{{ route('permintaan_produk.create') }}" class="btn btn-orange">
                        Permintaan Alat
                    </a>

                    <select name="kategori" class="filter-select" onchange="this.form.submit()">
                        <option value="">Filter Kategori</option>
                        @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->idkategori }}" {{ request('kategori') == $kategori->idkategori ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div class="produk-grid">
            @foreach($produkList as $produk)

            @php
                // Ambil stok cabang, kalau belum ada default 0
                $stok = optional($produk->stokCabang->first())->jumlah ?? 0;

                // Ambil path gambar, fallback ke placeholder jika tidak ada
                $gambar = $produk->gambar_produk && file_exists(public_path($produk->gambar_produk))
                    ? asset($produk->gambar_produk)
                    : asset('images/placeholder.png');
            @endphp

            <div class="produk-card">
                <span class="badge">
                    {{ $produk->kategori->nama_kategori }}
                </span>

                <img src="{{ $gambar }}" alt="{{ $produk->nama_produk }}">

                <h4>{{ $produk->nama_produk }}</h4>

                <p class="harga">
                    Rp {{ number_format($produk->harga) }} / hari
                </p>

                <span class="stok tersedia">
                    Stok: {{ $stok }}
                </span>
            </div>

            @endforeach
        </div>

        <div class="pagination">
           
        </div>
    </div>
</div>
@endsection
