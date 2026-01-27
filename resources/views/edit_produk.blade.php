@extends('layouts.app')

@php
    $active = 'produk';
@endphp

@section('title','Edit Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_produk.css') }}">
@endpush

@section('content')
<div class="container-tambah-produk">

    <div class="card-tambah-produk">
        <h3>Edit Produk</h3>

        <form action="{{ route('produk.update', $produk->idproduk) }}" 
              method="POST" 
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Nama Produk --}}
            <div class="form-group">
                <input type="text" 
                       name="nama_produk" 
                       placeholder="Nama Produk" 
                       value="{{ $produk->nama_produk }}" 
                       required>
            </div>

            {{-- Kategori --}}
            <div class="form-group">
                <select name="kategori_idkategori" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->idkategori }}"
                            {{ $produk->kategori_idkategori == $k->idkategori ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Stok Pusat --}}
            <div class="form-group">
                <input type="number" 
                       name="stok_pusat" 
                       placeholder="Stok Pusat" 
                       value="{{ $produk->stok_pusat }}" 
                       required>
            </div>

            {{-- Harga --}}
            <div class="form-group">
                <input type="number" 
                       name="harga" 
                       placeholder="Harga Sewa" 
                       value="{{ $produk->harga }}" 
                       required>
            </div>

            {{-- Jenis Skala --}}
            <div class="form-group">
                <select name="jenis_skala" required>
                    <option value="">Pilih Jenis Skala</option>
                    <option value="Skala Besar" {{ $produk->jenis_skala == 'Skala Besar' ? 'selected' : '' }}>Skala Besar</option>
                    <option value="Skala Kecil" {{ $produk->jenis_skala == 'Skala Kecil' ? 'selected' : '' }}>Skala Kecil</option>
                </select>
            </div>

            {{-- Upload Gambar --}}
            <div class="form-group upload-box">
                <label class="upload-label">
                    <input type="file" name="gambar_produk">
                    <span>Ganti gambar produk (opsional)</span>
                </label>

                @if($produk->gambar_produk)
                    <small>Gambar saat ini: {{ $produk->gambar_produk }}</small>
                @endif
            </div>

            {{-- Admin Pusat (hidden) --}}
            <input type="hidden" 
                   name="admin_pusat_idadmin_pusat" 
                   value="{{ $produk->admin_pusat_idadmin_pusat }}">

            {{-- Tombol --}}
            <div class="form-footer">
                <button type="submit" class="btn-simpan">Simpan Perubahan</button>
                <a href="{{ route('data_produk') }}" class="btn-batal">Batal</a>
            </div>

        </form>
    </div>

</div>
@endsection
