@extends('layouts.app')

@php
    $active = 'data.permintaan';
@endphp

@section('title','Data Permintaan Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_permintaan.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')

<div class="page-container">
<div class="section data-permintaan">

    <!-- ================= HEADER ================= -->
    <div class="data-header">

        <!-- kiri -->
        <div class="data-left">
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

        <!-- kanan -->
        <div class="data-right">
            <form method="GET">
                <input
                    type="text"
                    id="searchInput"
                    name="search"
                    class="search-table"
                    placeholder="Cari tanggal..."
                    value="{{ request('search') }}">
            </form>

            <a href="{{ route('permintaan_produk.create') }}" class="btn btn-orange">
                <i class="fa-solid fa-plus"></i> Ajukan
            </a>
        </div>

    </div>

    <!-- ================= TABLE ================= -->
    <div class="table-wrapper">

        <div class="table-header">
            <div class="col no">No</div>
            <div class="col tanggal">Tanggal</div>
            <div class="col total">Jumlah Item</div>
            <div class="col status">Status</div>
            <div class="col aksi">Aksi</div>
        </div>

        <div class="table-body">

        @foreach($permintaan as $item)

        <div class="table-row">

            <div class="col no">{{ $loop->iteration }}</div>

            <div class="col tanggal">
                {{ $item->tanggal_permintaan
                    ? \Carbon\Carbon::parse($item->tanggal_permintaan)->translatedFormat('l, d F Y')
                    : '-' }}
            </div>

            <div class="col total">
                {{ $item->produkDetail->count() }} alat
            </div>

            <div class="col status">
                @if ($item->status === 'menunggu')
                    <span class="badge waiting">
                        <i class="fa-solid fa-clock"></i> Menunggu
                    </span>
                @elseif ($item->status === 'disetujui')
                    <span class="badge success">
                        <i class="fa-solid fa-circle-check"></i> Disetujui
                    </span>
                @elseif ($item->status === 'ditolak')
                    <span class="badge danger">
                        <i class="fa-solid fa-circle-xmark"></i> Ditolak
                    </span>
                @elseif ($item->status === 'sampai')
                    <span class="badge info">
                        <i class="fa-solid fa-box"></i> Sampai
                    </span>
                @endif
            </div>

            <div class="col aksi">

                <!-- DETAIL -->
                <button
                    type="button"
                    class="btn btn-detail"
                    onclick="openModal({{ $item->idpermintaan }})">
                    <i class="fa-solid fa-eye"></i>
                </button>

                <!-- TERIMA -->
                @if(strtolower($item->status) === 'disetujui')
                <form action="{{ route('distribusi_produk.terima', $item->idpermintaan) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-confirm">
                        <i class="fa-solid fa-circle-check"></i> Terima
                    </button>
                </form>
                @endif

            </div>

        </div>

        @endforeach

        </div>
    </div>

    <!-- ================= PAGINATION ================= -->
    <div class="pagination-simple">
        @if ($permintaan->onFirstPage())
            <span class="nav disabled">«</span>
        @else
            <a href="{{ $permintaan->previousPageUrl() }}" class="nav">«</a>
        @endif

        @foreach ($permintaan->getUrlRange(1, $permintaan->lastPage()) as $page => $url)
            @if ($page == $permintaan->currentPage())
                <span class="page active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="page">{{ $page }}</a>
            @endif
        @endforeach

        @if ($permintaan->hasMorePages())
            <a href="{{ $permintaan->nextPageUrl() }}" class="nav">»</a>
        @else
            <span class="nav disabled">»</span>
        @endif
    </div>

</div>
</div>

<!-- ================= MODAL (DI LUAR TABLE) ================= -->
@foreach($permintaan as $item)
<div class="modal" id="modal-{{ $item->idpermintaan }}">
    <div class="modal-box">

        <div class="modal-header">
            <div>
                <h4>Detail Distribusi - {{ $item->cabang->nama_cabang ?? '-' }}</h4>
            </div>

            <span class="close" onclick="closeModal({{ $item->idpermintaan }})">
                <i class="fa-solid fa-xmark"></i>
            </span>
        </div>

        <div class="modal-body">
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Diminta</th>
                        <th>Dikirim</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($item->produkDetail as $j => $detail)
                    <tr>
                        <td>{{ $j+1 }}</td>
                        <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                        <td>{{ $detail->produk->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $detail->jumlah_diminta }}</td>
                        <td>
                            {{ optional($detail->distribusi->first())->jumlah_dikirim ?? '-' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
/* ================= SEARCH ================= */
const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(keyword) ? 'grid' : 'none';
    });
});

/* ================= MODAL ================= */
function openModal(id){
    document.getElementById('modal-'+id).style.display = 'flex';
}

function closeModal(id){
    document.getElementById('modal-'+id).style.display = 'none';
}

/* klik luar modal */
window.onclick = function(e){
    document.querySelectorAll('.modal').forEach(modal=>{
        if(e.target === modal){
            modal.style.display = 'none';
        }
    });
}
</script>
@endpush