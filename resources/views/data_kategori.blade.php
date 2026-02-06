@extends('layouts.app')

@php
    $active = 'kategori';
@endphp

@section('title','Data Kategori')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_kategori.css') }}">
@endpush

@section('content')

<div class="container-kategori">

    {{-- Header --}}
    <div class="header-kategori">
        <div class="left">
            <select>
                <option>20</option>
                <option>50</option>
                <option>100</option>
            </select>
            <span>Data Per Halaman</span>
        </div>

        <div class="right">
            <input type="text" placeholder="Pencarian...">
            <button type="button" class="btn-tambah" onclick="openTambahModal()">
                + Tambah
            </button>
        </div>
    </div>

    {{-- Table --}}
    <table class="table-kategori">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Kategori</th>
                <th>Stok Pusat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategori as $no => $item)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $item->nama_kategori }}</td>
                <td>{{ $item->produk_sum_stok_pusat ?? 0 }}</td>
                <td>
                    {{-- BUTTON EDIT: HARUS type="button" --}}
                    <button type="button" class="btn-edit"
                        onclick="openEditModal('{{ $item->idkategori }}','{{ $item->nama_kategori }}')">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    {{-- FORM DELETE --}}
                    <form action="{{ route('kategori.destroy', $item->idkategori) }}"
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-hapus" 
                                onclick="return confirm('Hapus kategori ini?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

{{-- Modal Tambah --}}
<div class="modal" id="modalTambah">
    <div class="modal-content">
        <h3>Tambah Kategori</h3>

        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" required>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-simpan">Tambah</button>
                <button type="button" class="btn-batal" onclick="closeTambahModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal" id="modalEdit">
    <div class="modal-content">
        <h3>Edit Kategori</h3>

        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" id="editNamaKategori" required>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-simpan">Simpan</button>
                <button type="button" class="btn-batal" onclick="closeEditModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openTambahModal() {
        document.getElementById('modalTambah').style.display = 'flex';
    }

    function closeTambahModal() {
        document.getElementById('modalTambah').style.display = 'none';
    }

    function openEditModal(id, nama) {
        document.getElementById('modalEdit').style.display = 'flex';
        document.getElementById('editNamaKategori').value = nama;

        let url = "{{ url('/data_kategori') }}/" + id;
        document.getElementById('formEdit').action = url;
    }

    function closeEditModal() {
        document.getElementById('modalEdit').style.display = 'none';
    }
</script>
@endpush