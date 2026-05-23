@extends('layouts.app')

@php
    $active = 'distribusi';
@endphp

@section('title','Distribusi Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/distribusi_produk.css') }}">
@endpush

@section('content')

<div class="kelola-container">

    {{-- ================= HEADER ================= --}}
    @if($view == 'permintaan')
    <div class="kelola-header">
        <h3>Kelola Permintaan dari Cabang</h3>

        <a href="{{ route('distribusi_produk', ['view'=>'riwayat']) }}"
           class="btn btn-orange">
            Riwayat
        </a>
    </div>
    @endif



    {{-- ================================================= --}}
    {{-- ================= PERMINTAAN ===================== --}}
    {{-- ================================================= --}}
    @if($view == 'permintaan')

        @forelse ($permintaan as $perm)
        <div class="permintaan-card">

            {{-- TOP INFO --}}
            <div class="permintaan-top">
                <div class="permintaan-info">
                    <strong>{{ $perm->cabang->nama_cabang ?? '-' }}</strong>
                    <span class="admin">
                        - Admin : {{ $perm->adminCabang->user->nama ?? '-' }}
                    </span>

                    <p class="tanggal">
                        Tanggal Permintaan:
                        {{ \Carbon\Carbon::parse($perm->created_at)->translatedFormat('d F Y H:i') }}
                    </p>

                    <p class="jumlah-produk">
                        Jumlah Produk :
                        <b>{{ $perm->produkDetail->count() }} jenis</b>
                    </p>

                    <p class="catatan">
                        <strong>Catatan :</strong>
                        {{ $perm->keterangan ?? '-' }}
                    </p>
                </div>

                <span class="status {{ strtolower($perm->status) }}">
                    {{ ucfirst($perm->status) }}
                </span>
            </div>


            {{-- FORM KIRIM --}}
            <div class="produk-box">

            @if($perm->status === 'menunggu')
            <form action="{{ route('distribusi_produk.kirim') }}" method="POST">
                @csrf
            @endif

            @foreach ($perm->produkDetail as $item)
                <div class="produk-item">

                    <h4>{{ $item->produk->nama_produk }}</h4>
                     @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                  @php
            $dikirim = $item->distribusi->sum('jumlah_dikirim') ?? 0;

            // kalau belum pernah kirim, pakai jumlah diminta
            $defaultInput = $dikirim > 0 ? $dikirim : $item->jumlah_diminta;
        @endphp

        {{-- DIMINTA --}}
        <p>Diminta : {{ $item->jumlah_diminta }}</p>


        {{-- INPUT --}}
        <input type="number"
            name="jumlah_dikirim[{{ $item->id }}]"
            value="{{ old('jumlah_dikirim.' . $item->id, $defaultInput) }}"
            min="1"
            max="{{ $item->jumlah_diminta }}"
            required>

                </div>
            @endforeach
             @if($perm->status === 'menunggu')

            <p class="catatan-label">Catatan Owner untuk Cabang</p>

            <input type="text"
                class="catatan-input"
                name="keterangan"
                placeholder="Contoh: Barang dikirim bertahap / stok terbatas">

            @else

            <p class="catatan-label">Catatan Owner untuk Cabang</p>

            <input type="text"
                class="catatan-input"
                value="{{ $item->distribusi->first()?->keterangan ?? '-' }}"
                readonly>

            @endif
            @if($perm->status === 'menunggu')
                <button type="submit" class="btn btn-green btn-full">
                    Setujui & Kirim
                </button>
            </form>
            @endif

            </div>
        </div>

        @empty
        <div class="empty">Belum ada permintaan distribusi</div>
        @endforelse

    @endif

    {{--RIWAYAT--}}
    @if($view == 'riwayat')

    <div class="kelola-header">

    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Pencarian...">
    </div>

    <a href="{{ route('distribusi_produk') }}"
       class="btn btn-red">
        Kembali <i class="fa-solid fa-arrow-right"></i>
    </a>

    </div>

    <div class="riwayat-wrapper">
        <h3 class="riwayat-title">Riwayat Distribusi Produk</h3>
        <table class="riwayat-table" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($riwayat as $i => $perm)
                    <tr>
                        <td>{{ $riwayat->firstItem() + $i }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($perm->created_at)->translatedFormat('d F Y H:i') }}
                        </td>

                        <td>{{ $perm->cabang->nama_cabang }}</td>
                        <td>{{ optional($perm->produkDetail)->count() ?? 0 }}</td>
                        <td>{{ ucfirst($perm->status) }}</td>

                        <td>
                            <button onclick="openModal({{ $perm->idpermintaan }})"
                                    class="btn btn-orange btn-sm">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">Belum ada riwayat distribusi produk</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
             @if(method_exists($riwayat, 'links'))
<div class="pagination-simple">

    {{-- Prev --}}
    @if ($riwayat->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $riwayat->previousPageUrl() }}" class="nav">«</a>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
        @if ($page == $riwayat->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if ($riwayat->hasMorePages())
        <a href="{{ $riwayat->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

</div>
@endif
        </div>
    </div>

{{-- ================= MODAL ================= --}}
@foreach($riwayat as $perm)
<div class="modal" id="modal-{{ $perm->idpermintaan }}">
    <div class="modal-box">

        {{-- HEADER --}}
        <div class="modal-header">

            <div class="modal-title">
                <h4>Detail Distribusi - {{ $perm->cabang->nama_cabang }}</h4>
                <p class="modal-admin">
                    Admin : {{ $perm->adminCabang->user->nama ?? '-' }}
                </p>
            </div>

            <span class="close"
                  onclick="closeModal({{ $perm->idpermintaan }})">
                <i class="fa-solid fa-xmark"></i>
            </span>

        </div>


        {{-- TABLE --}}
        <div class="modal-body">
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Diminta</th>
                        <th>Dikirim</th>
                        <th>Catatan Cabang</th>
                        <th>Catatan Owner</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($perm->produkDetail as $j => $item)
                    <tr>
                        <td>{{ $j+1 }}</td>
                        <td>{{ $item->produk->nama_produk }}</td>
                        <td>{{ $item->produk->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $item->jumlah_diminta }}</td>
                        <td>{{ optional(optional($item->distribusi)->first())->jumlah_dikirim ?? '-' }}</td>
                        <td>{{ $perm->keterangan ?? '-' }}</td>
                        <td>{{ optional(optional($item->distribusi)->first())->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endforeach

@endif 
<script>
function openModal(id){
    document.getElementById('modal-'+id).style.display='flex'
}
function closeModal(id){
    document.getElementById('modal-'+id).style.display='none'
}
document.getElementById('searchInput').addEventListener('keyup', function () {

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {

        let text = row.innerText.toLowerCase();

        row.style.display =
            text.includes(value)
            ? ''
            : 'none';
    });

});
</script>

@endsection
