@extends('layouts.app')

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

        <div class="data-left">
            <select class="per-page" id="perPage">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
            </select>
            <span class="per-page-text">Data per halaman</span>
        </div>

        <div class="data-right">
            <input
                type="text"
                id="searchInput"
                class="search-table"
                placeholder="Cari tanggal..."
            >

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
@foreach($permintaan as $items) <!-- $items = satu permintaan, bisa banyak produk -->

<div class="table-row">

    <div class="col no">{{ $no++ }}</div>

    <div class="col tanggal">
        {{ \Carbon\Carbon::parse($items->first()->tanggal_permintaan)->translatedFormat('d F Y') }}
    </div>

    <div class="col total">
        {{ $items->count() }} alat
    </div>

    <div class="col status">
        @if ($items->first()->status === 'menunggu')
            <span class="badge waiting">
                <i class="fa-solid fa-clock"></i> Menunggu
            </span>
        @elseif ($items->first()->status === 'disetujui')
            <span class="badge success">
                <i class="fa-solid fa-circle-check"></i> Disetujui
            </span>
        @elseif ($items->first()->status === 'ditolak')
            <span class="badge danger">
                <i class="fa-solid fa-circle-xmark"></i> Ditolak
            </span>
        @endif
    </div>

    <div class="col aksi">

        <!-- DETAIL -->
        <button
            class="btn btn-detail"
            data-modal="modal{{ $loop->index }}">
            <i class="fa-solid fa-eye"></i>
        </button>

        {{-- KONFIRMASI TERIMA BARANG --}}
        @if($items->first()->status === 'disetujui')
            <form action="{{ route('distribusi_produk.terima', $items->first()->idpermintaan) }}" method="POST">
                @csrf
                <button class="btn btn-confirm">
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
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->produk->nama_produk }}</td>
                            <td>{{ $item->jumlah_diminta }}</td>
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

</div>
</div>

@endsection

@push('scripts')
<script>
/* ================= SEARCH + LIMIT ================= */
const searchInput = document.getElementById('searchInput');
const perPage = document.getElementById('perPage');
const rows = document.querySelectorAll('.table-row');

function renderTable() {
    let keyword = searchInput.value.toLowerCase();
    let limit = parseInt(perPage.value);
    let shown = 0;

    rows.forEach(row => {
        const tanggal = row.querySelector('.tanggal').innerText.toLowerCase();

        if (tanggal.includes(keyword) && shown < limit) {
            row.style.display = 'grid';
            shown++;
        } else {
            row.style.display = 'none';
        }
    });
}

searchInput.addEventListener('keyup', renderTable);
perPage.addEventListener('change', renderTable);
renderTable();

/* ================= MODAL ================= */
document.querySelectorAll('[data-modal]').forEach(btn => {
    btn.onclick = () => {
        document.getElementById(btn.dataset.modal).classList.add('active');
    };
});

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.onclick = () => {
        btn.closest('.modal-custom').classList.remove('active');
    };
});

document.querySelectorAll('.modal-custom').forEach(modal => {
    modal.onclick = e => {
        if (e.target === modal) modal.classList.remove('active');
    };
});
</script>
@endpush
