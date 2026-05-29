@extends('layouts.app')

@php
    $active = 'kontrak';
@endphp

@section('title','Kontrak Franchise')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kontrak_franchise.css') }}">
@endpush

@section('content')

<div class="container-kontrak">

    <div class="card-kontrak">

        <img 
            src="{{ asset('storage/' . $adminCabang->gambar_mou) }}" 
            alt="MoU Franchise"
            class="gambar-mou"
        >

        <a href="{{ asset('storage/' . $adminCabang->gambar_mou) }}" 
           target="_blank"
           class="btn-mou">
            <i class="fa-solid fa-eye"></i> Perbesar
        </a>

    </div>

</div>

@endsection