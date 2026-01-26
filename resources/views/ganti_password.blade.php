@extends('layouts.app')

@section('title', 'Ganti Password')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ganti_password.css') }}">
@endpush


@section('content')

<div class="profile-container">

    <!-- TAB -->
    <div class="profile-tabs">

        <a href="/profil_cabang" class="tab">
            <i class="fa-solid fa-user"></i>
            Profile
        </a>

        <div class="tab active">
            <i class="fa-solid fa-lock"></i>
            <span>Ganti Password</span>
        </div>

        <div class="tab">
            <i class="fa-solid fa-building-columns"></i>
            <span>Rekening</span>
        </div>

    </div>

    <!-- FORM GANTI PASSWORD -->
    <form
        class="profile-form"
        method="POST"
        action="{{ route('ganti.password.update') }}"
    >
        @csrf

        <div class="form-row full">
            <input
                type="password"
                name="password_lama"
                placeholder="Password Lama"
                required
            >
        </div>

        <div class="form-row full">
            <input
                type="password"
                name="password_baru"
                placeholder="Password Baru"
                required
            >
        </div>

        <div class="form-row full">
            <input
                type="password"
                name="password_baru_confirmation"
                placeholder="Konfirmasi Password Baru"
                required
            >
        </div>

        <button type="submit" class="btn-simpan">
            Simpan Password
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
