@extends('layouts.app')

@section('title', 'Rekening')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/rekening.css')}}">
@endpush

@section('content')
<div class="profile-container">

    <!-- TAB MENU -->
    <div class="profile-tabs">
        <a href="/profil_cabang" class="tab">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </a>

        <a href="/ganti_password" class="tab">
            <i class="fa-solid fa-lock"></i>
            <span>Ganti Password</span>
        </a>

        <div class="tab active">
            <i class="fa-solid fa-money-check"></i>
            <span>Rekening</span>
        </div>
    </div>

    <!-- FORM REKENING -->
    <form
        class="profile-form"
        method="POST"
        action="{{ route('rekening.update') }}"
    >
        @csrf

        <!-- NAMA BANK -->
        <div class="form-row full">
            <input
                type="text"
                name="nama_bank"
                placeholder="Nama Bank"
                value="{{ old('nama_bank', $rekening->nama_bank ?? '') }}"
                required
            >
        </div>

        <!-- NO REKENING -->
        <div class="form-row full">
            <input
                type="text"
                name="no_rekening"
                placeholder="No. Rekening"
                value="{{ old('no_rekening', $rekening->no_rekening ?? '') }}"
                required
            >
        </div>

        <!-- ATAS NAMA -->
        <div class="form-row full">
            <input
                type="text"
                name="atas_nama"
                placeholder="Atas Nama"
                value="{{ old('atas_nama', $rekening->atas_nama ?? '') }}"
                required
            >
        </div>

        <button type="submit" class="btn-simpan">
            Simpan
        </button>

    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: "{{ session('success') }}",
    showConfirmButton: false,
    timer: 2000
});
</script>
@endif
@endpush
