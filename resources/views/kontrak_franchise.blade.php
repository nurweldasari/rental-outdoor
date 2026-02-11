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
        <a href="{{ asset('assets/uploads/mou/' . $adminCabang->gambar_mou) }}" target="_blank" class="btn-mou">
            <i class="fa-solid fa-eye"></i> Lihat MoU
        </a>
    </div>
</div>
@endsection
