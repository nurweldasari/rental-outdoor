@extends('layouts.app')

@php
    $active = 'riwayat';
@endphp

@section('title','Riwayat Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item_penyewaan.css') }}">
@endpush 

@section('content')
<div class="container-riwayat">

<div class="info-riwayat">
    Ini Menampilkan Penyewaan yang sudah dikembalikan
</div>

   <!-- ================= TAB SELESAI ================= -->
<div class="tab-content active" id="selesai">

    @forelse ($penyewaanSelesai as $item)
    <div class="card-riwayat selesai">
        <div class="card-left">
            <p><strong>Nama</strong> : {{ auth()->user()->nama }}</p>
            <p><strong>No. Telephone</strong> : {{ auth()->user()->no_telepon ?? '-' }}</p>
            <p><strong>Tanggal Sewa</strong> :
                {{ \Carbon\Carbon::parse($item->tanggal_sewa)->translatedFormat('d F Y') }}
            </p>
        </div>

        <div class="divider"></div>

        <div class="card-right">
            <p>Metode Pembayaran : <strong>{{ ucfirst($item->metode_bayar) }}</strong></p>
            <div class="status-wrapper">
    <span class="badge-selesai">Selesai</span>
</div>

            <p class="total">Total : Rp {{ number_format($item->total) }}</p>

            <div class="action">
                <a href="{{ route('detail_sewa', $item->idpenyewaan) }}" class="btn detail">Detail</a>
            </div>
        </div>
    </div>
    @empty
        <p class="empty">Tidak ada penyewaan selesai</p>
    @endforelse
</div>

</div>
@endsection