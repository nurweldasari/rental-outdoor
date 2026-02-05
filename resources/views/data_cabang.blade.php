@extends('layouts.app')

@php
    $active = 'cabang';
@endphp

@section('title', 'Data Cabang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_cabang.css') }}">
@endpush

@section('content')
<div class="container-cabang" id="containerCabang">

    {{-- HEADER --}}
    <div class="header-cabang">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Pencarian...">
    </div>

    <button class="btn-rekening" onclick="openRekeningModal()">
    <i class="fa-solid fa-plus"></i> Rekening Cabang
</button>
</div>


    {{-- TABLE --}}
    <div class="pagination-wrap">
    {{ $cabang->links() }}
    </div>
        <div class="table-top">
           <form method="GET">
            <select name="entries" onchange="this.form.submit()">
                <option value="20" {{ $entries == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ $entries == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $entries == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span>Data Per Halaman</span>
            </form>
        </div>
        <table class="table-cabang" id="dataTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Cabang</th>
                    <th>Lokasi Cabang</th>
                    <th>Nama Admin</th>
                    <th>Status</th>
                    <th>Konfirmasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            @foreach ($cabang as $i => $c)
                @php
                    $admin = $c->adminCabang->first();
                @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $c->nama_cabang }}</td>
                    <td>{{ $c->lokasi }}</td>
                    <td>{{ $admin?->user?->nama ?? '-' }}</td>

                   {{-- STATUS --}}
<td class="status-col">

@if (in_array($c->status_cabang, ['aktif','nonaktif']))
    <form action="{{ route('cabang.toggle', $c->idcabang) }}" method="POST">
        @csrf
        <button class="btn-toggle">
            @if ($c->status_cabang === 'aktif')
                <i class="fa-solid fa-toggle-on text-success"></i>
            @else
                <i class="fa-solid fa-toggle-off text-danger"></i>
            @endif
        </button>
    </form>

@elseif ($c->status_cabang === 'pending')
    <i class="fa-solid fa-toggle-off text-muted"></i>

@elseif ($c->status_cabang === 'ditolak')
    <i class="fa-solid fa-toggle-off text-danger"></i>

@endif

</td>


                   {{-- KONFIRMASI --}}
<td class="confirm-col">

@if ($c->status_cabang === 'pending')

    <div class="confirm-wrap">
        <form action="{{ route('cabang.tolak', $c->idcabang) }}" method="POST">
            @csrf
            <button type="submit" class="icon-btn">
                <i class="fa-solid fa-circle-xmark text-danger"></i>
            </button>
        </form>

        <form action="{{ route('cabang.terima', $c->idcabang) }}" method="POST">
            @csrf
            <button type="submit" class="icon-btn">
                <i class="fa-solid fa-circle-check text-success"></i>
            </button>
        </form>
    </div>

@elseif (in_array($c->status_cabang, ['aktif','nonaktif']))
    <i class="fa-solid fa-circle-check text-success"></i>

@elseif ($c->status_cabang === 'ditolak')
    <i class="fa-solid fa-circle-xmark text-danger"></i>

@endif

</td>

                    {{-- AKSI DETAIL --}}
                    <td>
                        <button type="button" class="btn-detail"
                        data-nama="{{ $admin?->user?->nama ?? '-' }}"
                        data-username="{{ $admin?->user?->username ?? '-' }}"
                        data-telp="{{ $admin?->user?->no_telepon ?? '-' }}"
                        data-alamat="{{ $admin?->user?->alamat ?? '-' }}"
                        data-cabang="{{ $c->nama_cabang }}"
                        data-lokasi="{{ $c->lokasi }}"
                        data-mou="{{ $admin?->gambar_mou ? asset('assets/uploads/mou/'.$admin->gambar_mou) : '' }}">
                        Detail
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal" id="modalDetail">
    <div class="modal-box">
        <h3>Informasi Data Cabang</h3>

        <div class="modal-body">
            <div class="modal-grid">
                <div>
                    <p><b>Nama</b><br><span id="mNama"></span></p>
                    <p><b>Username</b><br><span id="mUsername"></span></p>
                    <p><b>No Telephone</b><br><span id="mNo_Telepone"></span></p>
                    <p><b>Alamat</b><br><span id="mAlamat"></span></p>
                </div>
                <div>
                    <p><b>Nama Cabang</b><br><span id="mCabang"></span></p>
                    <p><b>Lokasi Cabang</b><br><span id="mLokasi"></span></p>
                </div>
            </div>

            <div class="mou-section">
                <label>Gambar MoU</label>
                {{-- Tombol buka MoU di tab baru --}}
                <button class="btn-mou" onclick="openMouTab()">{{ $admin?->gambar_mou ?? '' }}</button>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-close" onclick="closeModal()">Tutup</button>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
const modalDetail = document.getElementById('modalDetail');

const mNama       = document.getElementById('mNama');
const mUsername   = document.getElementById('mUsername');
const mTelp       = document.getElementById('mNo_Telepone'); 
const mAlamat     = document.getElementById('mAlamat');
const mCabang     = document.getElementById('mCabang');
const mLokasi     = document.getElementById('mLokasi');

let mouImage = '';

// Tampilkan modal detail
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.onclick = () => {
        mNama.innerText     = btn.dataset.nama || '-';
        mUsername.innerText = btn.dataset.username || '-';
        mTelp.innerText     = btn.dataset.telp || '-';
        mAlamat.innerText   = btn.dataset.alamat || '-';
        mCabang.innerText   = btn.dataset.cabang || '-';
        mLokasi.innerText   = btn.dataset.lokasi || '-';

        mouImage = btn.dataset.mou;

        modalDetail.style.display = 'flex';
    };
});

// Buka MoU di tab baru
function openMouTab() {
    if (!mouImage) return alert('Gambar MoU belum tersedia');
    window.open(mouImage, '_blank');
}

// Tutup modal
function closeModal() {
    modalDetail.style.display = 'none';
}

document.getElementById('searchInput').addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {
        row.style.display = row.innerText
            .toLowerCase()
            .includes(keyword)
            ? ''
            : 'none';
    });
});

function openRekeningModal() {
    document.getElementById('modalRekening').style.display = 'flex';
}

function closeRekeningModal() {
    document.getElementById('modalRekening').style.display = 'none';
}
</script>
@include('tambah_rekening')
@endsection
