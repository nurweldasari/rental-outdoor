@extends('layouts.app')

@php
    $active = 'riwayat';
@endphp

@section('title','Riwayat Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_penyewaan.css') }}">
@endpush 

@section('content')
<div class="container-data">

    <!-- INFO BOX -->
    <div class="info-riwayat">
        Ini Menampilkan Penyewaan yang sudah dikembalikan
    </div>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="left">
            <form id="filterForm" method="GET">
                <select class="per-page" name="per_page" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page')==10?'selected':'' }}>10</option>
                    <option value="25" {{ request('per_page')==25?'selected':'' }}>25</option>
                    <option value="50" {{ request('per_page')==50?'selected':'' }}>50</option>
                    <option value="100" {{ request('per_page')==100?'selected':'' }}>100</option>
                </select>
                <span>Data Per Halaman</span>

                <input type="text" 
                       class="search" 
                       name="search" 
                       placeholder="Pencarian..." 
                       value="{{ request('search') }}">
            </form>
        </div>
    </div>

    <!-- HEADER -->
    <div class="table-header">
        <div>No.</div>
        <div>Tanggal Reservasi</div>
        <div>Penyewa</div>
        <div>Total</div>
        <div>Status</div>
        <div>Detail</div>
    </div>

    <!-- ROW -->
    @forelse($riwayatList as $i => $p)
        <div class="table-row">
    <div>{{ $riwayatList->firstItem() + $i }}.</div>

    <div>
        {{ \Carbon\Carbon::parse($p->tanggal_sewa)->translatedFormat('l, d M Y') }}
    </div>

    <div>
        <strong>{{ optional($p->penyewa->user)->nama ?? '-' }}</strong>
        ({{ optional($p->penyewa->user)->no_telepon ?? '-' }})
    </div>

    <div>
        Rp {{ number_format($p->total) }}
    </div>

    <!-- STATUS (Tambahan baru) -->
    @php
    $statusClass = match($p->status_penyewaan) {
        'selesai' => 'done'
    };
@endphp

<div>
    <span class="status {{ $statusClass }}">
        {{ ucfirst(str_replace('_',' ', $p->status_penyewaan)) }}
    </span>
</div>


    <div>
        <a href="{{ route('admin.penyewaan.detail', $p->idpenyewaan) }}" 
           class="btn-detail">
            Detail
        </a>
    </div>
</div>

    @empty
        <p style="text-align:center;margin-top:40px;">
            Belum ada riwayat penyewaan
        </p>
    @endforelse


    <!-- PAGINATION -->
    <div class="pagination-simple">
        {{-- Prev --}}
        @if ($riwayatList->onFirstPage())
            <span class="nav disabled">«</span>
        @else
            <a href="{{ $riwayatList->previousPageUrl() }}" class="nav">«</a>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($riwayatList->getUrlRange(1, $riwayatList->lastPage()) as $page => $url)
            @if ($page == $riwayatList->currentPage())
                <span class="page active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="page">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($riwayatList->hasMorePages())
            <a href="{{ $riwayatList->nextPageUrl() }}" class="nav">»</a>
        @else
            <span class="nav disabled">»</span>
        @endif
    </div>

</div>
@endsection
