@extends('layouts.app')

@php
    $active = 'produk';
@endphp

@section('title','Tambah Paket Pusat')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/paket.css') }}">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')

<div class="section paket">
    <form action="{{ route('paket_pusat.store') }}"
      method="POST"
      enctype="multipart/form-data">
        @csrf

        <h3>Tambah Paket Pusat</h3>

        <!-- Nama Paket -->
        <div class="form-group">
            <label>Nama Paket</label>
            <input type="text" name="nama_paket" required>
        </div>

        <!-- Harga -->
        <div class="form-group">
            <label>Harga Paket</label>
            <input type="number" name="harga_paket" required>
        </div>

        <!-- Isi Paket -->
        <h4>Isi Paket</h4>

        <div id="produk-container">
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
        </div>

        <button type="button" class="btn-add" onclick="tambahProduk()">
            + Tambah Produk
        </button>
<div class="form-group">
    <label>Gambar Paket</label>
    <input type="file" name="gambar_paket">
</div>
        <br><br>

        <div class="form-footer">

    <button type="submit" class="btn-simpan">
        Simpan Paket
    </button>

    <a href="{{ route('data_produk') }}" class="btn-batal">
        Batal
    </a>

</div>

    </form>
</div>

@endsection

@push('scripts')
<script>
function tambahProduk() {
    let html = `
    <div class="produk-item">
        <select name="produk_id[]"required>
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

function hapusProduk(button) {
    button.parentElement.remove();
}
</script>
@endpush