@extends('layouts.app')

@section('title', 'Profil Cabang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profil_cabang.css') }}">
@endpush


@section('content')

<div class="profile-container">

    <!-- TAB MENU -->
    <div class="profile-tabs">

        <div class="tab active">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </div>

        <a href="/ganti_password" class="tab">
            <i class="fa-solid fa-lock"></i>
            <span>Ganti Password</span>
        </a>

        <div class="tab">
            <i class="fa-solid fa-money-check"></i>
            <span>Rekening</span>
        </div>

    </div>

    <!-- FORM PROFILE -->
    <form
        class="profile-form"
        method="POST"
        action="{{ route('profil.cabang.update') }}"
    >
        @csrf

        <!-- DATA CABANG -->
        <div class="form-row">
            <input
                type="text"
                name="nama_cabang"
                placeholder="Nama Cabang"
                value="{{ old('nama_cabang', $cabang->nama_cabang ?? '') }}"
                required
            >

            <input
                type="text"
                name="lokasi"
                placeholder="Lokasi Cabang"
                value="{{ old('lokasi', $cabang->lokasi ?? '') }}"
                required
            >
        </div>

        <!-- DATA ADMIN -->
        <div class="form-row">
            <input
                type="text"
                name="nama"
                placeholder="Nama Admin Cabang"
                value="{{ old('nama', $user->nama) }}"
                required
            >

            <input
                type="text"
                name="no_telepon"
                placeholder="No. Telephone Admin Cabang"
                value="{{ old('no_telepon', $user->no_telepon) }}"
                required
            >
        </div>

        <div class="form-row full">
            <input
                type="text"
                name="alamat"
                placeholder="Alamat Domisili Admin Cabang"
                value="{{ old('alamat', $user->alamat) }}"
                required
            >
        </div>

        <div class="form-row center">
            <input
                type="text"
                name="username"
                placeholder="Username"
                value="{{ old('username', $user->username) }}"
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
