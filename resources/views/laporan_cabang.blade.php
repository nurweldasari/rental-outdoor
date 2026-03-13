@extends('layouts.app')

@php 
    $active = 'laporan_cabang'; 
@endphp

@section('title','Laporan Cabang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@section('content')

{{-- ================== MODE LIST CABANG ================== --}}
@if(isset($cabangList))

<div class="container-cabang">
    @foreach ($cabangList as $c)
        <div class="cabang-card">
            <i class="fa-solid fa-location-dot"></i>
            <div class="card-title">{{ $c->nama_cabang }}</div>
            <a href="{{ route('laporan', ['cabang' => $c->idcabang]) }}" class="btn-lihat">
                Lihat Laporan
            </a>
        </div>
    @endforeach
</div>

@endif



@endsection