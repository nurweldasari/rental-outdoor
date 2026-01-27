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

    </div>
</div>

@endsection

@push('scripts')
<script>
/* ================= SEARCH + LIMIT ================= */
const searchInput = document.getElementById('searchInput');
const perPage = document.getElementById('perPage');

function renderTable() {
    const rows = Array.from(document.querySelectorAll('.table-row'));
    const keyword = searchInput.value.toLowerCase();
    const limit = parseInt(perPage.value);
    let shown = 0;

    rows.forEach(row => {
        const tanggal = row.querySelector('.tanggal')?.innerText.toLowerCase() || '';
        if(tanggal.includes(keyword) && shown < limit){
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
