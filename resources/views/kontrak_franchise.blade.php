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

        @php
            $file = asset('storage/' . $adminCabang->gambar_mou);
            $ext = strtolower(pathinfo($adminCabang->gambar_mou, PATHINFO_EXTENSION));
        @endphp

        @if($ext === 'pdf')
            <iframe
                src="{{ $file }}"
                width="100%"
                height="800px"
                style="border:none;">
            </iframe>
        @else
            <img
                src="{{ $file }}"
                alt="MoU Franchise"
                class="gambar-mou">
        @endif

    
</div>

@endsection