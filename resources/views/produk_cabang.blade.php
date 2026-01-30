@extends('layouts.app')

@php
    $active = 'produk.cabang';
@endphp

@section('title','Data Produk Cabang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_produk.css') }}">
@endpush

@section('content')
<div class="container-produk">

    {{-- HEADER (MENYAMAKAN PRODUK PUSAT) --}}
    <div class="header-produk">

    {{-- Search --}}
    <form method="GET" id="searchForm">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text"
               id="searchInput"
               name="search"
               placeholder="Pencarian..."
               value="{{ request('search') }}">
    </div>
</form>


    {{-- Tombol Permintaan Alat --}}
    <a href="{{ route('permintaan_produk.create') }}" class="btn-tambah">
        <i class="fa-solid fa-plus"></i> Permintaan Alat
    </a>

    {{-- Filter Kategori (DIPINDAH KE KANAN) --}}
    <form method="GET" id="filterForm">
        <select name="kategori" onchange="this.form.submit()">
            <option value="">Filter Kategori</option>
            @foreach($kategoriList as $kategori)
                <option value="{{ $kategori->idkategori }}"
                    {{ request('kategori') == $kategori->idkategori ? 'selected' : '' }}>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
    </form>

</div>


    {{-- GRID PRODUK --}}
    <div class="grid-produk">

        @forelse($produkList as $produk)

            @php
    // ambil stok cabang milik cabang login
    $stokCabang = $produk->stokCabang->first();

    $stok = $stokCabang->jumlah ?? 0;

    $gambarPath = 'assets/uploads/produk/'.$produk->gambar_produk;

    $gambar = $produk->gambar_produk &&
              file_exists(public_path($gambarPath))
        ? asset($gambarPath)
        : asset('images/placeholder.png');
@endphp


            <div class="card-produk">

    {{-- Badge Kategori --}}
    <span class="badge-kategori">
        {{ $produk->kategori->nama_kategori ?? '-' }}
    </span>

    {{-- Gambar --}}
    <img src="{{ $gambar }}"
         alt="{{ $produk->nama_produk }}"
         class="img-produk">

    {{-- Nama --}}
    <h4>{{ $produk->nama_produk }}</h4>

    {{-- Harga --}}
    <p class="harga">
        Rp {{ number_format($produk->harga) }} / hari
    </p>

   
    {{-- STOK + STATUS --}}
<div class="stok-wrapper">

    <span class="stok-text">
        Stok: {{ $stok }}
    </span>

    @if($stokCabang)
        <form action="{{ route('produk_cabang.toggle', $stokCabang->idstok) }}"
              method="POST"
              class="toggle-form">
            @csrf

            <button type="submit"
                    class="btn-toggle {{ $stokCabang->is_active ? 'aktif' : 'nonaktif' }}"
                    title="{{ $stokCabang->is_active ? 'Nonaktifkan produk' : 'Aktifkan produk' }}">

                <i class="fa-solid
                    {{ $stokCabang->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}">
                </i>

            </button>
        </form>
    @endif

</div>

</div>


        @empty
            <p>Tidak ada produk tersedia di cabang ini.</p>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if(method_exists($produkList, 'links'))
        <div class="pagination">
            {{ $produkList->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
const searchInput = document.getElementById('searchInput');
let searchTimeout;

searchInput.addEventListener('keyup', function () {
    // Hapus timeout sebelumnya
    clearTimeout(searchTimeout);

    // Delay 400ms agar tidak submit terlalu sering
    searchTimeout = setTimeout(() => {
        document.getElementById('searchForm').submit();
    }, 400);
});
</script>
@endpush