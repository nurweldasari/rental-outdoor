@extends('layouts.app')

@php
    $active = 'produk';
@endphp

@section('title','Edit Paket')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/paket.css') }}">
@endpush

@section('content')

<div class="section paket">
    <form action="{{ route('paket_pusat.update', $paket->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <h3>Edit Paket</h3>

        {{-- Nama Paket --}}
        <div class="form-group">
            <label>Nama Paket</label>
            <input type="text"
                   name="nama_paket"
                   value="{{ $paket->nama_paket }}"
                   required>
        </div>

        {{-- Harga --}}
        <div class="form-group">
            <label>Harga Paket</label>
            <input type="number"
                   name="harga_paket"
                   value="{{ $paket->harga_paket }}"
                   required>
        </div>

        {{-- Isi Paket --}}
        <h4>Isi Paket</h4>

        <div id="produk-container">

            @foreach($paket->detail as $detail)
            <div class="produk-item">
                <select name="produk_id[]" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produk as $item)
                        <option value="{{ $item->idproduk }}"
                            {{ $detail->produk_idproduk == $item->idproduk ? 'selected' : '' }}>
                            {{ $item->nama_produk }} (Stok: {{ $item->stok_pusat }})
                        </option>
                    @endforeach
                </select>

                <input type="number"
                       name="qty[]"
                       value="{{ $detail->qty }}"
                       min="1"
                       required>

                <button type="button"
                        class="btn-remove"
                        onclick="hapusProduk(this)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            @endforeach

        </div>

        <button type="button"
                class="btn-add"
                onclick="tambahProduk()">
            + Tambah Produk
        </button>

        {{-- Gambar --}}
        <div class="form-group">
            <label>Gambar Paket</label>
            <input type="file" name="gambar_paket">
        </div>

        <br>

        {{-- Tombol --}}
        <div class="form-footer">
        <button type="submit" class="btn-simpan">
            Simpan Perubahan
        </button>

        <a href="{{ route('data_produk') }}" class="btn-batal">
            Batal
        </a>
</div>

    </form>
</div>
<script>
function tambahProduk() {
    let html = `
    <div class="produk-item">
        <select name="produk_id[]" required>
            <option value="">-- Pilih Produk --</option>
            @foreach($produk as $item)
                <option value="{{ $item->idproduk }}">
                    {{ $item->nama_produk }} (Stok: {{ $item->stok_pusat }})
                </option>
            @endforeach
        </select>

        <input type="number" name="qty[]" placeholder="Qty" min="1" required>

        <button type="button" class="btn-remove" onclick="hapusProduk(this)">
            <i class="fa fa-trash"></i>
        </button>
    </div>
    `;

    document.getElementById('produk-container')
        .insertAdjacentHTML('beforeend', html);
}
function hapusProduk(btn) {
    btn.parentElement.remove();
}
</script>
@endsection