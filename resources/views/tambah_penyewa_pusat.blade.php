@extends('layouts.app')

@section('title', 'Data Penyewa')

@php
    $active = 'penyewa';
@endphp 

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tambah_penyewa.css') }}">
@endpush


@section('content')

<div class="tambah-penyewa-page">

  <div class="form-card">
    <h3 class="form-title">Tambah Penyewa</h3>

    <form 
        class="penyewa-form"
        method="POST"
        action="{{ route('tambah_penyewa_pusat.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="form-group">
            <input type="text" name="nama" placeholder="Nama" required>
        </div>

        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Password" id="password" required>
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
        </div>

        <div class="form-group">
            <input type="text" name="no_telepon" placeholder="No. Telephone" required>
        </div>

        <div class="form-group">
            <input type="text" name="alamat" placeholder="Alamat" required>
        </div>

        <!-- UPLOAD -->
        <div class="upload-box" onclick="document.getElementById('gambar').click()">
            <i class="fa-solid fa-cloud-arrow-up"></i>

            <span id="uploadText" class="upload-text">
                Upload gambar identitas
            </span>

            <input 
                type="file"
                name="gambar_identitas"
                id="gambar"
                accept="image/*"
                hidden
                onchange="showFileName(this)">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Tambah Penyewa</button>
            <a href="{{ route('data_penyewa_pusat') }}" class="btn-cancel">Batal</a>
        </div>

    </form>
  </div>

</div>


@endsection


@push('scripts')
<script>
function showFileName(input) {
    if (input.files.length > 0) {
        document.getElementById('uploadText').innerText =
            input.files[0].name;

        document.querySelector('.upload-box')
            .classList.add('file-selected');
    }
}
function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>
@endpush