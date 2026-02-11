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
                        <b>{{ $perm->produkPermintaan->count() }} jenis</b>
                    </p>
                </div>

                <span class="status {{ strtolower($perm->status) }}">
                    {{ ucfirst($perm->status) }}
                </span>
            </div>


            {{-- FORM KIRIM --}}
            @if($perm->status === 'menunggu')
            <div class="produk-box">
                <form action="{{ route('distribusi_produk.kirim') }}" method="POST">
                    @csrf

                    @foreach ($perm->produkPermintaan as $item)
                        <div class="produk-item">
                            <h4>{{ $item->produk->nama_produk }}</h4>
                            <p class="diminta">Diminta : {{ $item->jumlah_diminta }} unit</p>

                            <label>Jumlah Dikirim</label>
                            <input type="number"
                                   name="jumlah_dikirim[{{ $item->id }}]"
                                   value="{{ $item->jumlah_diminta }}"
                                   min="0"
                                   required>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-green btn-full">
                        Setujui & Kirim
                    </button>
                </form>
            </div>
            @endif

        </div>

        @empty
        <div class="empty">Belum ada permintaan distribusi</div>
        @endforelse

    @endif

    {{--RIWAYAT--}}
    @if($view == 'riwayat')

    <div class="riwayat-card">
    <div class="kelola-header">
        <a href="{{ route('distribusi_produk') }}"
           class="btn btn-red">
            Kembali
        </a>
    </div>

    <div class="riwayat-wrapper">
        <h3 class="riwayat-title">Riwayat Distribusi Produk</h3>
        <table class="riwayat-table">
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
                        <td>{{ $i+1 }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($perm->created_at)->translatedFormat('d F Y H:i') }}
                        </td>

                        <td>{{ $perm->cabang->nama_cabang }}</td>
                        <td>{{ $perm->produkPermintaan->count() }}</td>
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
                        <td colspan="6">Belum ada riwayat</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
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
                    </tr>
                </thead>

                <tbody>
                @foreach($perm->produkPermintaan as $j => $item)
                    <tr>
                        <td>{{ $j+1 }}</td>
                        <td>{{ $item->produk->nama_produk }}</td>
                        <td>{{ $item->produk->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $item->jumlah_diminta }}</td>
                        <td>{{ $item->distribusi->first()->jumlah_dikirim ?? '-' }}</td>
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
</script>

@endsection
