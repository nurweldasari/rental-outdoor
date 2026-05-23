@extends('layouts.app')

@php
    $active = 'kategori';
@endphp

@section('title','Data Kategori')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_kategori.css') }}">
@endpush

@section('content')

<div class="container-kategori">

    {{-- Header --}}
    <div class="header-kategori">
        <div class="left">
            <form method="GET">
                <select class="per-page" name="per_page" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page')==10?'selected':'' }}>10</option>
                    <option value="25" {{ request('per_page')==25?'selected':'' }}>25</option>
                    <option value="50" {{ request('per_page')==50?'selected':'' }}>50</option>
                    <option value="100" {{ request('per_page')==100?'selected':'' }}>100</option>
                </select>
            </form>
            <span class="per-page-text">Data per halaman</span>
        </div>

        <div class="right">
            <input type="text" id="searchInput" placeholder="Pencarian...">
            <button type="button" class="btn-tambah" onclick="openTambahModal()">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </div>
    </div>

    {{-- Table --}}
    {{-- Table --}}
<div class="table-wrapper">
    <table class="table-kategori">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Kategori</th>
                <th>Stok Pusat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kategori as $no => $item)
            <tr>
                <td>{{ $kategori->firstItem() + $no }}</td>
                <td>{{ $item->nama_kategori }}</td>
                <td>{{ $item->produk_sum_stok_pusat ?? 0 }}</td>
                <td>
                    <button type="button" class="btn-edit"
                        onclick="openEditModal('{{ $item->idkategori }}','{{ $item->nama_kategori }}')">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    <form action="{{ route('kategori.destroy', $item->idkategori) }}"
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-hapus" 
                                onclick="return confirm('Hapus kategori ini?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
           @empty
            <tr>
                <td colspan="4" class="empty-table">Belum ada data kategori. Tambahkan kategori terlebih dahulu.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah --}}
<div class="modal" id="modalTambah">
    <div class="modal-content">
        <h3>Tambah Kategori</h3>

        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <input type="text" name="nama_kategori" placeholder="Nama Kategori" required>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-simpan">Tambah</button>
                <button type="button" class="btn-batal" onclick="closeTambahModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal" id="modalEdit">
    <div class="modal-content">
        <h3>Edit Kategori</h3>

        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <input type="text" name="nama_kategori" placeholder="Nama Kategori"id="editNamaKategori" required>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-simpan">Simpan</button>
                <button type="button" class="btn-batal" onclick="closeEditModal()">Batal</button>
            </div>
        </form>
    </div>
</div>
@if(method_exists($kategori, 'links'))
<div class="pagination-simple">

    {{-- Prev --}}
    @if ($kategori->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $kategori->previousPageUrl() }}" class="nav">«</a>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($kategori->getUrlRange(1, $kategori->lastPage()) as $page => $url)
        @if ($page == $kategori->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if ($kategori->hasMorePages())
        <a href="{{ $kategori->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

</div>
@endif
@endsection

@push('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.table-kategori tbody tr');

        rows.forEach(function(row) {
            let namaKategori = row.children[1].textContent.toLowerCase();

            if (namaKategori.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function openTambahModal() {
        document.getElementById('modalTambah').style.display = 'flex';
    }

    function closeTambahModal() {
        document.getElementById('modalTambah').style.display = 'none';
    }

    function openEditModal(id, nama) {
        document.getElementById('modalEdit').style.display = 'flex';
        document.getElementById('editNamaKategori').value = nama;

        let url = "{{ url('/data_kategori') }}/" + id;
        document.getElementById('formEdit').action = url;
    }

    function closeEditModal() {
        document.getElementById('modalEdit').style.display = 'none';
    }
</script>
@endpush