@extends('layouts.app')

@php
    $active = 'produk';
@endphp

@section('title','Data Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_produk.css') }}">
@endpush

@section('content')
<div class="container-produk">

    {{-- HEADER --}}
    <div class="header-produk">
        {{-- Search --}}
        <input type="text" class="search" placeholder="Pencarian...">

        {{-- Filter skala --}}
        <select class="filter">
            <option>Pilih Jenis Skala</option>
            <option>Harian</option>
            <option>Mingguan</option>
            <option>Bulanan</option>
        </select>

        {{-- Tombol tambah --}}
        <a href="{{ route('tambah_produk') }}" class="btn-tambah">
            <span>+</span> Tambah Produk
        </a>
    </div>

    {{-- Filter Kategori --}}
    <select class="filter">
        <option value="">Filter Kategori</option>
        @foreach($kategori as $kat)
            <option value="{{ $kat->id_kategori }}">
                {{ $kat->nama_kategori }}
            </option>
        @endforeach
    </select>

    {{-- GRID PRODUK --}}
    <div class="grid-produk">
        @foreach($produk as $item)
        <div class="card-produk">

        <span class="badge-kategori">
        {{ $item->kategori->nama_kategori ?? 'Tidak ada kategori' }}
    </span>

            {{-- DROPDOWN AKSI --}}
            <div class="dropdown">
                <button type="button"
                        class="btn-dot"
                        onclick="toggleDropdown({{ $item->idproduk }})">
                    ⋮
                </button>

                <div class="dropdown-menu" id="dropdown-{{ $item->idproduk }}">
                    <a href="{{ route('produk.update', $item->idproduk) }}">
                        ✏️ Edit Produk
                    </a>

                    <form action="{{ route('produk.destroy', $item->idproduk) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Hapus produk ini?')">
                            🗑️ Hapus Produk
                        </button>
                    </form>
                </div>
            </div>

            {{-- GAMBAR --}}
            <img src="{{ $item->gambar_produk
                ? asset('assets/uploads/produk/'.$item->gambar_produk)
                : asset('images/no-image.png') }}"
                alt="{{ $item->nama_produk }}"
                class="img-produk">

            {{-- NAMA --}}
            <h4>{{ $item->nama_produk }}</h4>

            {{-- HARGA --}}
            <p class="harga">
                Rp {{ number_format($item->harga, 0, ',', '.') }}
            </p>

            {{-- STOK --}}
            <span class="stok">
                Stok: {{ $item->stok_pusat }}
            </span>

        </div>
        @endforeach
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleDropdown(id) {
    document.querySelectorAll('.dropdown-menu')
        .forEach(menu => menu.style.display = 'none');

    const dropdown = document.getElementById('dropdown-' + id);
    dropdown.style.display = dropdown.style.display === 'block'
        ? 'none'
        : 'block';
}

window.addEventListener('click', function(e) {
    if (!e.target.classList.contains('btn-dot')) {
        document.querySelectorAll('.dropdown-menu')
            .forEach(menu => menu.style.display = 'none');
    }
});
</script>
@endpush

