@extends('layouts.app')

@php
    $active = 'laporan';
@endphp

@section('title','Laporan Pendapatam')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@section('content')
<div class="laporan-container">

    <div class="laporan-top">

        <!-- Pilih Periode -->
        <div class="filter-box">
            Pilih Periode
            <span>📅</span>
        </div>

        <button class="btn-cetak" onclick="window.print()">
            Cetak Laporan
        </button>
    </div>


    <!-- HEADER -->
     <div class="laporan-table">
    <div class="laporan-header">
        <div>No.</div>
        <div>Tanggal sewa</div>
        <div>Penyewa</div>
        <div>Item Produk</div>
        <div>Total</div>
    </div>

    @php $no = 1; @endphp

    @foreach($penyewaan as $data)
    <div class="laporan-row">
        <div>{{ $no++ }}.</div>

        <div>
            {{ \Carbon\Carbon::parse($data->tanggal_sewa)->translatedFormat('l, d M Y') }}
        </div>

        <div>
            <strong>{{ $data->penyewa->user->nama }}</strong>
            ({{ $data->penyewa->user->no_telepon }})
        </div>

        <div class="produk-list">
            @foreach($data->itemPenyewaan as $item)
                <div>
                    {{ $loop->iteration }}.
                    {{ $item->produk->nama_produk }}
                    ({{ $item->qty }})
                </div>
            @endforeach
        </div>

        <div>
            Rp {{ number_format($data->total,0,',','.') }}
        </div>
    </div>
    @endforeach

    <!-- TOTAL -->
    <div class="laporan-total">
        <div></div>
        <div></div>
        <div></div>
        <div class="total-label">Total Pendapatan</div>
        <div class="total-value">
            Rp {{ number_format($totalPendapatan,0,',','.') }}
        </div>
    </div>
</div>
</div>
@endsection