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

    <!-- HEADER -->
    <div class="kelola-header">
        <h3>Kelola Permintaan dari Cabang</h3>
        <a href="{{ route('distribusi_produk') }}" class="btn btn-orange">
            Riwayat
        </a>
    </div>

    {{-- ================= LOOP PERMINTAAN ================= --}}
   @foreach ($permintaan as $items)
<div class="permintaan-card">

    <!-- TOP INFO -->
    <div class="permintaan-top">
        <div class="permintaan-info">
            <strong>{{ $items[0]->cabang->nama_cabang }}</strong>
            <span class="admin">- Admin : {{ $items[0]->adminCabang->user->nama ?? '-' }}</span>
            <p class="tanggal">
                Tanggal Permintaan :
                {{ \Carbon\Carbon::parse($items[0]->tanggal_permintaan)->translatedFormat('d F Y') }}
            </p>
        </div>

        <span class="status menunggu">Menunggu Persetujuan</span>
    </div>

    <!-- PRODUK -->
    <div class="produk-box">

        <form action="{{ route('distribusi_produk.kirim') }}" method="POST">
            @csrf

            @foreach ($items as $item)
                <h4>{{ $item->produk->nama_produk }}</h4>
                <p class="diminta">
                    Diminta : {{ $item->jumlah_diminta }} unit
                </p>

                <label>Jumlah yang Dikirim</label>
                <input
                    type="number"
                    name="jumlah_dikirim[{{ $item->idpermintaan }}]"
                    min="0"
                    max="{{ min($item->jumlah_diminta, $item->produk->stok_pusat) }}"
                    value="{{ $item->jumlah_diminta }}"
                    required
                >
            @endforeach

            <button type="submit" class="btn btn-green btn-full" style="margin-top:12px;">
                Setujui & Kirim Semua Produk
            </button>
        </form>

    </div>

</div>
@endforeach


</div>

@endsection
