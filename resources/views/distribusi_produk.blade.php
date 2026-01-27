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

    {{-- LOOP PERMINTAAN --}}
    @foreach ($permintaan as $perm)
    <div class="permintaan-card">

        <!-- TOP INFO -->
        <div class="permintaan-top">
            <div class="permintaan-info">
                <strong>{{ $perm->cabang->nama_cabang ?? '-' }}</strong>
                <span class="admin">- Admin : {{ $perm->adminCabang->user->nama ?? '-' }}</span>
                <p class="tanggal">
                    Tanggal Permintaan : {{ \Carbon\Carbon::parse($perm->tanggal_permintaan)->translatedFormat('d F Y') }}
                </p>
                <p class="jumlah-produk">
                    Jumlah Produk: <b>{{ $perm->produkPermintaan->count() }} jenis</b>
                </p>
            </div>

            <span class="status {{ strtolower($perm->status ?? 'menunggu') }}">
                @if($perm->status === 'menunggu') Menunggu Persetujuan
                @elseif($perm->status === 'disetujui') Disetujui
                @elseif($perm->status === 'ditolak') Ditolak
                @elseif($perm->status === 'sampai') Sampai
                @endif
            </span>
        </div>

        <!-- PRODUK -->
        @if(strtolower($perm->status) === 'menunggu')
        <div class="produk-box">
            <form action="{{ route('distribusi_produk.kirim') }}" method="POST">
                @csrf
                @foreach ($perm->produkPermintaan as $item)
                    <div class="produk-item">
                        <h4>{{ $item->produk->nama_produk ?? '-' }}</h4>
                        <p class="diminta">Diminta : {{ $item->jumlah_diminta }} unit</p>

                        <label>Jumlah yang Dikirim</label>
                        <input
                            type="number"
                            name="jumlah_dikirim[{{ $item->id }}]"
                            min="0"
                            max="{{ min($item->jumlah_diminta, $item->produk->stok_pusat ?? 0) }}"
                            value="{{ $item->jumlah_diminta }}"
                            required
                        >
                    </div>
                @endforeach

                <button type="submit" class="btn btn-green btn-full" style="margin-top:12px;">
                    Setujui & Kirim Semua Produk
                </button>
            </form>
        </div>
        @endif <!-- Tutup IF menunggu -->

    </div>
    @endforeach <!-- Tutup LOOP permintaan -->

</div>

@endsection
