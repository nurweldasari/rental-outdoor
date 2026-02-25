@extends('layouts.app')

@php
    $active = 'bagi_hasil';
@endphp

@section('title', 'Bagi Hasil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/bagi_hasil.css') }}">
@endpush

@section('content')

<div class="container">

@if(!isset($cabangTerpilih))
    {{-- ================= LIST CABANG ================= --}}
    <h4 class="mb-4">Bagi Hasil</h4>

    <div class="row">
        @foreach($cabangs as $cabang)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm p-3 text-center">
                    <h6>{{ $cabang->nama_cabang }}</h6>

                    <a href="{{ route('bagi_hasil', $cabang->idcabang) }}"
                       class="btn btn-warning btn-sm mt-2">
                        Hitung Bagi Hasil
                    </a>
                </div>
            </div>
        @endforeach
    </div>

@else

<div class="d-flex justify-content-between align-items-center mb-3">

    <div class="d-flex gap-2">
        <a href="#" class="btn btn-warning btn-sm">Bukti Fee</a>
        <a href="#" class="btn btn-warning btn-sm">Riwayat Bagi Hasil</a>
    </div>

    <a href="#" class="btn btn-warning btn-sm">Pengaturan Skala</a>
</div>

<div class="card p-4 shadow-sm">

    {{-- BOX REKENING --}}
    <div class="rekening-box mb-3 p-3">
        <strong>Rekening Tujuan Fee :</strong><br>
        Mandiri - 98767896540 <br>
        a.n OwnerOutdoorKriss
    </div>

    <h5 class="text-center mb-4">Detail Perhitungan Bagi Hasil</h5>

    <form action="{{ route('bagi_hasil.store') }}" method="POST">
        @csrf

        <input type="hidden" name="cabang_id" value="{{ $cabangTerpilih->idcabang }}">

        {{-- TOTAL --}}
        <div class="perhitungan-box mb-3">
            <div class="d-flex justify-content-between">
                <span>Total Pendapatan Cabang</span>
                <strong>Rp {{ number_format($totalPendapatan,0,',','.') }}</strong>
            </div>
        </div>

        {{-- OWNER --}}
        <div class="perhitungan-box mb-3">
            <strong>Owner ({{ $persenOwner }}%)</strong>

            <div class="d-flex justify-content-between mt-2">
                <span>Perhitungan:</span>
                <span>
                    Rp {{ number_format($totalPendapatan,0,',','.') }}
                    × {{ $persenOwner }}%
                </span>
            </div>

            <div class="d-flex justify-content-between mt-1">
                <strong>Hasil :</strong>
                <strong>
                    Rp {{ number_format($hasilOwner,0,',','.') }}
                </strong>
            </div>
        </div>

        {{-- CABANG --}}
        <div class="perhitungan-box mb-4">
            <strong>Admin Cabang ({{ $persenCabang }}%)</strong>

            <div class="d-flex justify-content-between mt-2">
                <span>Perhitungan:</span>
                <span>
                    Rp {{ number_format($totalPendapatan,0,',','.') }}
                    × {{ $persenCabang }}%
                </span>
            </div>

            <div class="d-flex justify-content-between mt-1">
                <strong>Hasil :</strong>
                <strong>
                    Rp {{ number_format($hasilCabang,0,',','.') }}
                </strong>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">
            Simpan
        </button>

    </form>

</div>

@endif