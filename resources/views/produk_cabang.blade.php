@extends('layouts.app')

@php
    $active = 'produk.cabang';
@endphp

@section('title','Data Produk Cabang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/produk_cabang.css') }}">
@endpush

@section('content')

<div class="page-container">

    <div class="section data-produk">

        <!-- ================= HEADER ================= -->
        <div class="produk-header">
            <form method="GET">

                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Pencarian..."
                    value="{{ request('search') }}"
                >

                <div class="produk-actions">

                    <a href="{{ route('permintaan_produk.create') }}"
                       class="btn btn-orange">
                        Permintaan Alat
                    </a>

                    <select name="kategori"
                            class="filter-select"
                            onchange="this.form.submit()">

                        <option value="">Filter Kategori</option>

                        @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->idkategori }}"
                                {{ request('kategori') == $kategori->idkategori ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                </div>
            </form>
        </div>

        <!-- ================= PRODUK ================= -->
        <div class="produk-grid">

            @forelse($produkList as $produk)

                @php
    $stok = $produk->stokCabang->sum('jumlah');

    $gambarPath = 'assets/uploads/produk/'.$produk->gambar_produk;

    $gambar = $produk->gambar_produk &&
              file_exists(public_path($gambarPath))
        ? asset($gambarPath)
        : asset('images/placeholder.png');
@endphp


                <div class="produk-card">

                    <span class="badge">
                        {{ $produk->kategori->nama_kategori ?? '-' }}
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

            @empty
                <p class="no-data">
                    Belum ada produk tersedia di cabang ini.
                </p>
            @endforelse

        </div>

        <!-- ================= PAGINATION ================= -->
        @if(method_exists($produkList, 'links'))
            <div class="pagination">
                {{ $produkList->withQueryString()->links() }}
            </div>
        @endif

    </div>

</div>

@endsection
