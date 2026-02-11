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
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Pencarian...">
    </div>

    {{-- Filter Skala --}}
    <select name="skala" form="filterForm" onchange="document.getElementById('filterForm').submit()">
        <option value="">Semua Skala</option>
        <option value="Skala Besar" {{ request('skala') == 'Skala Besar' ? 'selected' : '' }}>Skala Besar</option>
        <option value="Skala Kecil" {{ request('skala') == 'Skala Kecil' ? 'selected' : '' }}>Skala Kecil</option>
    </select>

    {{-- Tombol Tambah Produk --}}
    <a href="{{ route('tambah_produk') }}" class="btn-tambah">
        <i class="fa-solid fa-plus"></i> Tambah Produk
    </a>

    {{-- Filter Kategori --}}
    <select name="kategori" form="filterForm" onchange="document.getElementById('filterForm').submit()">
        <option value="">Filter Kategori</option>
        @foreach($kategori as $kat)
            <option value="{{ $kat->idkategori }}" {{ request('kategori') == $kat->idkategori ? 'selected' : '' }}>
                {{ $kat->nama_kategori }}
            </option>
        @endforeach
    </select>

    {{-- Form hidden --}}
    <form id="filterForm" method="GET" action="{{ route('data_produk') }}"></form>
</div>


    {{-- GRID PRODUK --}}
    <div class="grid-produk">
        @forelse ($produk as $item)
            <div class="card-produk">

                {{-- Badge Kategori --}}
                <span class="badge-kategori">{{ $item->kategori->nama_kategori }}</span>

                {{-- Dropdown Aksi --}}
                <div class="dropdown-produk">
                    <button type="button"
                            class="btn-dot"
                            onclick="toggleDropdown({{ $item->idproduk }})">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>

                    <div class="dropdown-menu-produk" id="dropdown-{{ $item->idproduk }}">
                        <a href="{{ route('produk.update', $item->idproduk) }}">
                            <i class="fa-solid fa-pen-to-square"></i>Edit Produk
                        </a>

                        <form action="{{ route('produk.destroy', $item->idproduk) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-hapus" onclick="return confirm('Hapus produk ini?')">
                                <i class="fa-solid fa-trash"></i> Hapus Produk
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Gambar --}}
                <img src="{{ $item->gambar_produk
                    ? asset('assets/uploads/produk/'.$item->gambar_produk)
                    : asset('images/no-image.png') }}"
                    alt="{{ $item->nama_produk }}"
                    class="img-produk">

                {{-- Nama --}}
                <h4>{{ $item->nama_produk }}</h4>

                {{-- Harga --}}
                <p class="harga">Rp. {{ number_format($item->harga, 0, ',', '.') }}/hari</p>

                {{-- Stok --}}
                <span class="stok">Stok: {{ $item->stok_pusat }}</span>
            </div>
        @empty
            <p class="empty">Belum ada data produk. Silakan tambahkan produk terlebih dahulu.</p>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleDropdown(id) {
    document.querySelectorAll('.dropdown-menu-produk')
        .forEach(menu => menu.style.display = 'none');

    const dropdown = document.getElementById('dropdown-' + id);
    dropdown.style.display = dropdown.style.display === 'block'
        ? 'none'
        : 'block';
}

window.addEventListener('click', function(e) {
    if (!e.target.classList.contains('btn-dot')) {
        document.querySelectorAll('.dropdown-menu-produk')
            .forEach(menu => menu.style.display = 'none');
    }
});
document.getElementById('searchInput').addEventListener('keyup', function () {
  let value = this.value.toLowerCase();
  let rows = document.querySelectorAll('.card-produk');

  rows.forEach(row => {
    let text = row.innerText.toLowerCase();
    row.style.display = text.includes(value) ? '' : 'none';
  });
});

document.getElementById('searchInput').addEventListener('keyup', function () {
    let value = this.value.toLowerCase();
    document.querySelectorAll('.card-produk').forEach(row => {
        row.style.display =
            row.innerText.toLowerCase().includes(value)
                ? ''
                : 'none';
    });
});
</script>
@endpush