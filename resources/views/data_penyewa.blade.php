@extends('layouts.app')

@php
    $active = 'penyewa';
@endphp

@section('title', 'Data Penyewa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_penyewa.css') }}">
@endpush

@section('content')

<div class="page-container">

    <!-- HEADER -->
    <div class="page-header">

        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Pencarian...">
        </div>

        <a href="/tambah_penyewa" class="btn-add">
            <i class="fa-solid fa-plus"></i>
            Tambah Penyewa
        </a>

    </div>

    <!-- TABLE -->
    <div class="table-wrapper">

        <table id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($penyewa as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->alamat }}</td>
                    <td>{{ $p->no_telepon }}</td>
                    <td class="aksi">
                        <button class="btn-green">Reservasi</button>

                        <button
                            class="btn-yellow btn-detail"
                            data-nama="{{ $p->nama }}"
                            data-username="{{ $p->username }}"
                            data-telepon="{{ $p->no_telepon }}"
                            data-alamat="{{ $p->alamat }}"
                            data-foto="{{ asset('assets/uploads/identitas/'.$p->gambar_identitas) }}">
                            Detail
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

{{-- ✅ MODAL — CLASS ASLI, CSS TETAP --}}
<div class="modal-overlay" id="modalDetailPenyewa">

  <div class="modal-box">

    <!-- HEADER -->
    <div class="modal-header">
      <span>Identitas Penyewa</span>
    </div>

    <!-- BODY -->
    <div class="modal-body">

      <!-- KIRI -->
      <div class="modal-left">
        <img id="modalFoto" src="" alt="Gambar Identitas">
        <p>Gambar Identitas</p>
      </div>

      <!-- KANAN -->
      <div class="modal-right">

        <div class="form-group">
          <span class="field-label">Nama</span>
          <input type="text" id="modalNama" disabled>
        </div>

        <div class="form-group">
          <span class="field-label">Username</span>
          <input type="text" id="modalUsername" disabled>
        </div>

        <div class="form-group">
          <span class="field-label">No. Telephone</span>
          <input type="text" id="modalTelepon" disabled>
        </div>

        <div class="form-group">
          <span class="field-label">Alamat</span>
          <input type="text" id="modalAlamat" disabled>
        </div>

      </div>

      <div id="imgPreviewOverlay">
        <span onclick="closeImgPreview()">&times;</span>
        <img>
      </div>

    </div>

    <!-- FOOTER -->
    <div class="modal-footer">
      <button class="btn-close" onclick="closeModal()">Tutup</button>
    </div>

  </div>

</div>

@endsection
@push('scripts')
<script>
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.onclick = () => {
        modalNama.value = btn.dataset.nama;
        modalUsername.value = btn.dataset.username;
        modalTelepon.value = btn.dataset.telepon;
        modalAlamat.value = btn.dataset.alamat;
        modalFoto.src = btn.dataset.foto;

        modalDetailPenyewa.style.display = 'flex';
    };
});

function closeModal() {
    document.getElementById("modalDetailPenyewa").style.display = "none";
}
const modalFoto = document.getElementById('modalFoto');
const overlay = document.getElementById('imgPreviewOverlay');
const overlayImg = overlay.querySelector('img');

modalFoto.addEventListener('click', () => {
  overlayImg.src = modalFoto.src;
  overlay.style.display = 'flex';
});

function closeImgPreview() {
  overlay.style.display = 'none';
}

document.getElementById('searchInput').addEventListener('keyup', function () {
  let value = this.value.toLowerCase();
  let rows = document.querySelectorAll('#dataTable tbody tr');

  rows.forEach(row => {
    let text = row.innerText.toLowerCase();
    row.style.display = text.includes(value) ? '' : 'none';
  });
});

document.getElementById('searchInput').addEventListener('keyup', function () {
    let value = this.value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(row => {
        row.style.display =
            row.innerText.toLowerCase().includes(value)
                ? ''
                : 'none';
    });
});
</script>
@endpush
