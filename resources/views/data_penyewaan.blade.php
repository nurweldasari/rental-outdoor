@extends('layouts.app')

@php
    $active = 'penyewaan';
@endphp

@section('title','Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_penyewaan.css') }}">
@endpush 

@section('content')
<div class="container-data">

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

                <input type="text" class="search" name="search" placeholder="Pencarian..." value="{{ request('search') }}">
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
        <div>Konfirmasi</div>
        <div>Aksi</div>
    </div>

    <!-- ROW -->
    @forelse($penyewaanList as $i => $p)
        <div class="table-row">
            <div>{{ $penyewaanList->firstItem() + $i }}.</div>
            <div>{{ \Carbon\Carbon::parse($p->tanggal_sewa)->translatedFormat('l, d M Y') }}</div>
            <div>
    <strong>{{ optional($p->penyewa->user)->nama ?? '-' }}</strong>
    ({{ optional($p->penyewa->user)->no_telepon ?? '-' }})
</div>


            <div>Rp {{ number_format($p->total) }}</div>
            <div>
                @php
                    $statusClass = match($p->status_penyewaan) {
                        'menunggu_pembayaran' => 'pending',
                        'sedang_disewa'       => 'active',
                        'selesai'             => 'done',
                        'dibatalkan'          => 'cancel',
                        default               => ''
                    };
                    $statusText = match($p->status_penyewaan) {
                        'menunggu_pembayaran' => 'Menunggu',
                        'sedang_disewa'       => 'Sedang Disewa',
                        'selesai'             => 'Selesai',
                        'dibatalkan'          => 'Dibatalkan',
                        default               => '-'
                    };
                @endphp
                <span class="status {{ $statusClass }}">{{ $statusText }}</span>
            </div>

            <div class="confirm">

@if($p->status_penyewaan === 'menunggu_pembayaran')

    <div class="confirm-wrap">

        {{-- Cancel --}}
    <form action="{{ route('admin.penyewaan.cancel', $p->idpenyewaan) }}" method="POST">
        @csrf
        <button type="submit" class="icon-btn cancel-btn">
            ✖
        </button>
    </form>

    {{-- Konfirmasi --}}
    <form action="{{ route('admin.konfirmasi_bayar', $p->idpenyewaan) }}" method="POST">
        @csrf
        <button type="submit" class="icon-btn ok-btn">
            ✔
        </button>
    </form>

    </div>

@elseif($p->status_penyewaan === 'sedang_disewa')
    <span class="icon ok">✔</span>

@elseif($p->status_penyewaan === 'dibatalkan')
    <span class="icon cancel">✖</span>

@elseif($p->status_penyewaan === 'selesai')
    <span class="icon ok">✔</span>
@endif

</div>
            <div>
                <div>
    <a href="{{ route('admin.penyewaan.detail', $p->idpenyewaan) }}" 
       class="btn-detail">
        Detail
    </a>
</div>

            </div>
        </div>
    @empty
        <p style="text-align:center;margin-top:40px;">Belum ada riwayat penyewaan</p>
    @endforelse

  <div class="pagination-simple">
    {{-- Prev --}}
    @if ($penyewaanList->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $penyewaanList->previousPageUrl() }}" class="nav">«</a>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($penyewaanList->getUrlRange(1, $penyewaanList->lastPage()) as $page => $url)
        @if ($page == $penyewaanList->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if ($penyewaanList->hasMorePages())
        <a href="{{ $penyewaanList->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif
</div>



</div>
@endsection


