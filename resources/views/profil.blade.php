@extends('layouts.app')

@section('title', 'Profil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush


@section('content')

  <!-- CONTENT -->
<div class="content-wrapper">

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
    </div>

    <!-- FORM PROFILE PENYEWA -->
    <form
        class="profile-form"
        method="POST"
        action="{{ route('profil.update') }}"
    >
      @csrf

      <!-- NAMA -->
      <div class="form-row full">
        <input
          type="text"
          name="nama"
          placeholder="Nama"
          value="{{ old('nama', $user->nama) }}"
          required
        >
      </div>

      <!-- ALAMAT -->
      <div class="form-row full">
        <input
          type="text"
          name="alamat"
          placeholder="Alamat"
          value="{{ old('alamat', $user->alamat) }}"
          required
        >
      </div>

      <!-- USERNAME -->
      <div class="form-row full">
        <input
          type="text"
          name="username"
          placeholder="Username"
          value="{{ old('username', $user->username) }}"
          required
        >
      </div>

      <!-- TELEPHONE -->
      <div class="form-row full">
        <input
          type="text"
          name="no_telepon"
          placeholder="No. Telephone"
          value="{{ old('no_telepon', $user->no_telepon) }}"
          required
        >
      </div>

      <button type="submit" class="btn-simpan">
        Simpan
      </button>

    </form>

  </div>
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
