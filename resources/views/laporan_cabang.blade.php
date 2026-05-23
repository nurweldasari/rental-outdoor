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
@if(method_exists($cabangList, 'links'))

<div class="pagination-simple">

    @if ($cabangList->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $cabangList->previousPageUrl() }}" class="nav">«</a>
    @endif

    @foreach ($cabangList->getUrlRange(1, $cabangList->lastPage()) as $page => $url)

        @if ($page == $cabangList->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif

    @endforeach

    @if ($cabangList->hasMorePages())
        <a href="{{ $cabangList->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

</div>

@endif
@endif
@endsection