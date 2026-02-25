@extends('layouts.app')

@section('title','Ajukan Permintaan Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/permintaan_alat.css') }}">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')

<div class="section permintaan">
    <h3>Ajukan Permintaan Produk</h3>

    {{-- Error validasi --}}
    @if ($errors->any())
        <div class="alert alert-red">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Flash message --}}
    @if(session('success'))
        <div class="alert alert-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-red">{{ session('error') }}</div>
    @endif

    <div class="form-box">

        <!-- HEADER + TOMBOL TAMBAH (ATAS) -->
        <div class="form-header">
            <span>Daftar Alat yang diminta</span>
            <button type="button" id="tambah-permintaan" class="btn btn-orange small">
                <i class="fa fa-plus"></i> Tambah
            </button>
        </div>

        <form action="{{ route('permintaan_produk.store') }}" method="POST">
            @csrf

            <!-- BOX ORANGE -->
            <div id="daftar-permintaan">
                <div class="form-item">

    <!-- Produk -->
    <select name="produk_id[]" class="produk-dropdown" required>
        <option value="">Pilih Produk</option>
        @foreach($produkList as $produk)
            <option 
                value="{{ $produk->idproduk }}"
                data-stok="{{ $produk->stok_pusat }}">
                {{ $produk->nama_produk }}
            </option>
        @endforeach
    </select>

    <!-- BOX STOK -->
    <div class="stok-box">
        Stok pusat: <span class="stok-value">-</span>
    </div>

    <!-- Jumlah -->
    <input type="number"
           name="jumlah_diminta[]"
           placeholder="Jumlah diminta"
           min="1"
           required>

    <!-- Hapus -->
    <button type="button" class="btn-delete">
        <i class="fa fa-trash"></i>
    </button>

</div>
            </div>

            <!-- CATATAN (PUTIH) -->
            <textarea name="keterangan" placeholder="Catatan (opsional)"></textarea>

            <!-- ACTION -->
            <div class="form-actions">
                <button type="submit" class="btn btn-green">Buat Permintaan</button>
                <button type="reset" class="btn btn-red">Batal</button>
            </div>

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

    newItem.querySelector('.produk-dropdown').selectedIndex = 0;
    newItem.querySelector('input[type=number]').value = '';
    newItem.querySelector('.stok-value').innerText = '-';

    container.appendChild(newItem);
});

// =====================
// Hapus row (min 1)
// =====================
container.addEventListener('click', function(e){
    if(e.target.closest('.btn-delete')) {
        const items = container.querySelectorAll('.form-item');
        if(items.length > 1) {
            e.target.closest('.form-item').remove();
        }
    }
});

// =====================
// Reset form
// =====================
btnReset.addEventListener('click', function(e) {
    e.preventDefault();

    const items = container.querySelectorAll('.form-item');
    items.forEach((item, index) => {
        if(index === 0){
            item.querySelector('.produk-dropdown').selectedIndex = 0;
            item.querySelector('input[type=number]').value = '';
        } else {
            item.remove();
        }
    });

    const textarea = document.querySelector('textarea[name="keterangan"]');
    if(textarea) textarea.value = '';
});

// =====================
// Update stok saat produk dipilih
// =====================
container.addEventListener('change', function(e){
    if(e.target.classList.contains('produk-dropdown')) {

        const selectedOption = e.target.options[e.target.selectedIndex];
        const stok = selectedOption.getAttribute('data-stok');

        const formItem = e.target.closest('.form-item');
        const stokValue = formItem.querySelector('.stok-value');

        if(stok){
            stokValue.innerText = stok;
        } else {
            stokValue.innerText = '-';
        }
    }
});
</script>
@endpush
