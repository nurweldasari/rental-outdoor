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

    <div class="right-header">

        <a href="{{ route('paket_cabang') }}" class="btn-tambah-paket">
            <i class="fa-solid fa-plus"></i> Tambah Paket
        </a>

        <a href="{{ route('permintaan_produk.create') }}" class="btn-tambah">
            <i class="fa-solid fa-plus"></i> Permintaan Alat
        </a>

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

</div>


    {{-- GRID PRODUK --}}
    <div class="grid-produk">

    {{-- ================= PAKET ================= --}}
    @forelse($paketList as $paket)

    @php
        $gambar = $paket->gambar_paket
        ? asset('storage/'.$paket->gambar_paket)
        : asset('images/placeholder.png');
    @endphp

    <div class="card-produk paket">

    {{-- TITIK 3 --}}
    <div class="dropdown-produk">
        <button type="button"
                class="btn-dot"
                onclick="toggleDropdown({{ $paket->id }})">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>

        <div class="dropdown-menu-produk"
             id="dropdown-{{ $paket->id }}">

            {{-- EDIT --}}
            <a href="{{ route('paket.edit', $paket->id) }}">
                <i class="fa-solid fa-pen-to-square"></i> Edit Paket
            </a>

            {{-- HAPUS --}}
            <form action="{{ route('paket.destroy', $paket->id) }}"
                  method="POST">
                @csrf
                @method('DELETE')

                <button type="submit"
                        class="btn-hapus"
                        onclick="return confirm('Yakin hapus paket ini?')">
                    <i class="fa-solid fa-trash"></i> Hapus Paket
                </button>
            </form>

        </div>
    </div>
        <span class="badge-kategori">Paket</span>

        <img src="{{ $gambar }}" class="img-produk">

        <h4>{{ $paket->nama_paket }}</h4>

        <p class="harga">
            Rp {{ number_format($paket->harga_paket) }}
        </p>

        <button class="btn-detail"
    onclick="openModal(this)"
    data-nama="{{ $paket->nama_paket }}"
    data-harga="{{ $paket->harga_paket }}"
    data-gambar="{{ $paket->gambar_paket
            ? asset('storage/'.$paket->gambar_paket)
            : asset('images/placeholder.png') }}"
    data-detail="
        @foreach($paket->detail as $item)
            {{ optional($item->stokCabang->produk)->nama_produk }} ({{ $item->qty }})|
        @endforeach
    ">
    Lihat Detail
</button>
    </div>

@empty

@endforelse


    {{-- ================= PRODUK ================= --}}
    @forelse($produkList as $produk)

        @php
            $stokCabang = $produk->stokCabang->first();
            $stok = $stokCabang->jumlah ?? 0;

            $gambar = $produk->gambar_produk
                ? asset('storage/'.$produk->gambar_produk)
                : asset('images/placeholder.png');
        @endphp

        <div class="card-produk">

            <span class="badge-kategori">
                {{ $produk->kategori->nama_kategori ?? '-' }}
            </span>

            <img src="{{ $gambar }}" class="img-produk">

            <h4>{{ $produk->nama_produk }}</h4>

            <p class="harga">
                Rp {{ number_format($produk->harga) }} / hari
            </p>

            <div class="stok-wrapper">
                <span class="stok">
                    Stok: {{ $stok }}
                </span>

                @if($stokCabang)
                    <form action="{{ route('produk_cabang.toggle', $stokCabang->idstok) }}"
                          method="POST">
                        @csrf

                        <button type="submit"
                                class="btn-toggle {{ $stokCabang->is_active ? 'aktif' : 'nonaktif' }}">
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
<div id="modalDetail" class="modal-paket">
    <div class="modal-content">

        <span class="close" onclick="closeModal()">&times;</span>

        <img id="modalGambar" class="modal-img">

        <div class="modal-body">
            <h3 id="modalNama"></h3>

            <p id="modalHarga" class="modal-harga"></p>

            <div id="modalIsi"></div>
        </div>

    </div>
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

function openModal(el) {
    let nama = el.getAttribute('data-nama');
    let harga = el.getAttribute('data-harga');
    let gambar = el.getAttribute('data-gambar');
    let detail = el.getAttribute('data-detail');

    let list = detail.split('|');

    let html = '';
    list.forEach(item => {
        if (item.trim() !== '') {
            html += `<div>• ${item}</div>`;
        }
    });

    document.getElementById('modalNama').innerText = nama;
    document.getElementById('modalHarga').innerText = "Rp " + Number(harga).toLocaleString();
    document.getElementById('modalGambar').src = gambar;
    document.getElementById('modalIsi').innerHTML = html;

    document.getElementById('modalDetail').style.display = 'block';
}

function closeModal() {
    document.getElementById('modalDetail').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('modalDetail');
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
function toggleDropdown(id) {

    document.querySelectorAll('.dropdown-menu-produk')
        .forEach(menu => menu.style.display = 'none');

    const menu = document.getElementById('dropdown-' + id);

    if (menu) {
        menu.style.display =
            menu.style.display === 'block'
                ? 'none'
                : 'block';
    }
}
</script>
@endpush