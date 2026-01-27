@extends('layouts.app')

@php
    $active = 'produk.cabang';
@endphp

@section('title','Ajukan Permintaan Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/permintaan_alat.css') }}">
@endpush

@section('content')

<div class="section permintaan">
    <h3>Ajukan Permintaan Produk</h3>

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-red">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tampilkan flash message --}}
    @if(session('success'))
        <div class="alert alert-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-red">{{ session('error') }}</div>
    @endif

    <div class="form-box">
        <form action="{{ route('permintaan_produk.store') }}" method="POST">
            @csrf

            <div id="daftar-permintaan">
                <div class="form-item">
                    <!-- Pilih Produk -->
                    <select name="produk_id[]" class="produk-dropdown" required>
                        <option value="">Pilih Produk</option>
                        @foreach($produkList as $produk)
                            <option value="{{ $produk->idproduk }}">{{ $produk->nama_produk }}</option>
                        @endforeach
                    </select>

                    <!-- Jumlah -->
                    <input type="number" name="jumlah_diminta[]" placeholder="Jumlah" min="1" required>

                    <!-- Hapus -->
                    <button type="button" class="btn-delete">🗑</button>
                </div>
            </div>

            <textarea name="keterangan" placeholder="Catatan (opsional)"></textarea>

            <div class="form-actions">
                <button type="submit" class="btn btn-green">Buat Permintaan</button>
                <button type="reset" class="btn btn-red">Batal</button>
            </div>

            <button type="button" id="tambah-permintaan" class="btn btn-orange small">+ Tambah</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const container = document.getElementById('daftar-permintaan');
const btnTambah = document.getElementById('tambah-permintaan');
const btnReset = document.querySelector('button[type=reset]');

// =====================
// Tambah row baru
// =====================
btnTambah.addEventListener('click', function() {
    const firstItem = container.querySelector('.form-item');
    const newItem = firstItem.cloneNode(true);

    // Reset input di row baru
    newItem.querySelector('.produk-dropdown').selectedIndex = 0;
    newItem.querySelector('input[type=number]').value = '';

    container.appendChild(newItem);
});

// =====================
// Hapus row (minimal 1 row tersisa)
// =====================
container.addEventListener('click', function(e){
    if(e.target.classList.contains('btn-delete')) {
        const items = container.querySelectorAll('.form-item');
        if(items.length > 1) {
            const row = e.target.closest('.form-item');
            if(row) row.remove();
        }
    }
});

// =====================
// Reset form: kembalikan hanya 1 row kosong & reset jumlah dan select
// =====================
btnReset.addEventListener('click', function(e) {
    e.preventDefault(); // agar JS reset bisa dijalankan
    const items = container.querySelectorAll('.form-item');
    items.forEach((item, index) => {
        if(index === 0){
            item.querySelector('.produk-dropdown').selectedIndex = 0;
            item.querySelector('input[type=number]').value = '';
        } else {
            item.remove();
        }
    });

    // Reset textarea juga
    const textarea = document.querySelector('textarea[name="keterangan"]');
    if(textarea) textarea.value = '';
});
</script>
@endpush
