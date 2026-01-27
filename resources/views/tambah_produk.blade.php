@extends('layouts.app')

@php
    $active = 'produk';
@endphp

@section('title','Tambah Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_produk.css') }}">
@endpush

@section('content')
<div class="container-tambah-produk">

    <div class="card-tambah-produk">
        <h3>Tambah Produk</h3>

        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nama Produk --}}
            <div class="form-group">
                <input type="text" name="nama_produk" placeholder="Nama Produk" required>
            </div>

            {{-- Kategori --}}
            <div class="form-group">
                <select name="kategori_idkategori" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->idkategori }}">
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Stok Pusat --}}
            <div class="form-group">
                <input type="number" name="stok_pusat" placeholder="Stok Pusat" required>
            </div>

            {{-- Harga --}}
            <div class="form-group">
                <input type="number" name="harga" placeholder="Harga Sewa" required>
            </div>

            {{-- Jenis Skala --}}
            <div class="form-group">
                <select name="jenis_skala" required>
                    <option value="">Pilih Jenis Skala</option>
                    <option value="Skala Besar">Skala Besar</option>
                    <option value="Skala Kecil">Skala Kecil</option>
                </select>
            </div>

            {{-- Upload Gambar --}}
            <div class="form-group upload-box">
                <label class="upload-label">
                    <input type="file" name="gambar_produk">
                    <span>Upload gambar produk</span>
                </label>
            </div>

            {{-- Admin Pusat (hidden) --}}
            <input type="hidden" 
                   name="admin_pusat_idadmin_pusat" 
                   value="{{ auth()->user()->idadmin_pusat ?? 1 }}">

            {{-- Tombol --}}
            <div class="form-footer">
                <button type="submit" class="btn-simpan">Tambah Produk</button>
                <a href="{{ route('data_produk') }}" class="btn-batal">Batal</a>
            </div>

        </form>
    </div>

</div>
@endsection
