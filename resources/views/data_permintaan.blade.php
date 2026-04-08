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
                value="{{ request('search') }}"
            >
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

            <div class="table-body" id="tableBody">

                @php $no = 1; @endphp
                @foreach($permintaan as $item) <!-- satu item = satu permintaan -->

                <div class="table-row">

                    <div class="col no">{{ $no++ }}</div>

                    <div class="col tanggal">
                        {{ $item->tanggal_permintaan
                            ? \Carbon\Carbon::parse($item->tanggal_permintaan)->translatedFormat('d F Y')
                            : '-' }}
                    </div>

                    <div class="col total">
                        {{ $item->produkDetail->count() }} alat
                    </div>

                    <div class="col status">
                        @php $status = $item->status; @endphp
                        @if ($status === 'menunggu')
                            <span class="badge waiting">
                                <i class="fa-solid fa-clock"></i> Menunggu
                            </span>
                        @elseif ($status === 'disetujui')
                            <span class="badge success">
                                <i class="fa-solid fa-circle-check"></i> Disetujui
                            </span>
                        @elseif ($status === 'ditolak')
                            <span class="badge danger">
                                <i class="fa-solid fa-circle-xmark"></i> Ditolak
                            </span>
                        @elseif ($status === 'sampai')
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
                            data-modal="modal{{ $loop->index }}">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                       {{-- KONFIRMASI TERIMA BARANG --}}
{{-- KONFIRMASI TERIMA BARANG --}}
@if(strtolower($item->status) === 'disetujui')
    <form action="{{ route('distribusi_produk.terima', $item->idpermintaan) }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-confirm">
        <i class="fa-solid fa-check"></i> Terima Barang
    </button>
</form>


@endif



                    </div>
                </div>

                <!-- ================= MODAL ================= -->
                <div class="modal-custom" id="modal{{ $loop->index }}">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4>Detail Permintaan</h4>
                            <span class="modal-close">&times;</span>
                        </div>

                        <div class="modal-body">
                            <table class="modal-table">
                                <thead>
                                    <tr>
                                        <th>Nama Alat</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->produkDetail as $detail)
                                        <tr>
                                            <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                                            <td>{{ $detail->jumlah_diminta }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                @endforeach

            </div>
        </div>
<div class="pagination-simple">
    {{-- Prev --}}
    @if ($permintaan->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $permintaan->previousPageUrl() }}" class="nav">«</a>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($permintaan->getUrlRange(1, $permintaan->lastPage()) as $page => $url)
        @if ($page == $permintaan->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if ($permintaan->hasMorePages())
        <a href="{{ $permintaan->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif
</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ================= SEARCH + LIMIT ================= */
const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase(); // 🔥 cari semua isi row

        if (text.includes(keyword)) {
            row.style.display = 'grid';
        } else {
            row.style.display = 'none';
        }
    });
});

/* ================= MODAL ================= */
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', () => {
        const modalId = btn.dataset.modal;
        const modal = document.getElementById(modalId);
        if(modal) modal.classList.add('active');
    });
});

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const modal = btn.closest('.modal-custom');
        if(modal) modal.classList.remove('active');
    });
});

document.querySelectorAll('.modal-custom').forEach(modal => {
    modal.addEventListener('click', e => {
        if(e.target === modal) modal.classList.remove('active');
    });
});
</script>
@endpush
