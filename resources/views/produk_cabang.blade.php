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
            @forelse($produkList as $produk)
                @php
                    // Hitung stok cabang yang sedang login
                    $stok = $produk->stokCabang
                        ->where('cabang_idcabang', auth()->user()->cabang_idcabang ?? 0)
                        ->sum('jumlah');

                    // Ambil gambar, fallback ke placeholder
                    $gambar = $produk->gambar_produk && file_exists(public_path($produk->gambar_produk))
                        ? asset($produk->gambar_produk)
                        : asset('images/placeholder.png');
                @endphp

                <div class="produk-card">
                    <span class="badge">{{ $produk->kategori->nama_kategori ?? '-' }}</span>

                    <img src="{{ $gambar }}" alt="{{ $produk->nama_produk }}">

                    <h4>{{ $produk->nama_produk }}</h4>

                    <p class="harga">
                        Rp {{ number_format($produk->harga) }} / hari
                    </p>

                    <span class="stok tersedia">
                        Stok: {{ $stok }}
                    </span>
                </div>

            @empty
                <p class="no-data">Belum ada produk tersedia.</p>
            @endforelse
        </div>

        @if(method_exists($produkList, 'links'))
            <div class="pagination">
                {{ $produkList->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
