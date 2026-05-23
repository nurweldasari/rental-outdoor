@extends('layouts.app')

@php $active = 'bagi_hasil'; @endphp
@section('title','Bagi Hasil Owner')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/bagi_hasil.css') }}">
@endpush

@section('content')

<div class="bagi-container">

    {{-- ================= LIST CABANG ================= --}}
    @if($view == 'list')
    <div class="cabang-grid">
        @foreach($cabangs as $cabang)
            <a href="{{ route('bagi_hasil.detail',$cabang->idcabang) }}"
               class="cabang-card">

                <i class="fa-solid fa-location-dot"></i>
                <h6>{{ $cabang->nama_cabang }}</h6>

                <div class="btn-orange">
                    Hitung Bagi Hasil
                </div>
            </a>
        @endforeach
    </div>
    @if(method_exists($cabangs, 'links'))

<div class="pagination-simple">

    @if ($cabangs->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $cabangs->previousPageUrl() }}" class="nav">«</a>
    @endif

    @foreach ($cabangs->getUrlRange(1, $cabangs->lastPage()) as $page => $url)

        @if ($page == $cabangs->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif

    @endforeach

    @if ($cabangs->hasMorePages())
        <a href="{{ $cabangs->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

</div>

@endif
    @endif


    {{-- ================= DETAIL ================= --}}
   @if($view == 'detail' && isset($cabangTerpilih))

{{-- TOP BUTTONS --}}
<div class="detail-header">

    {{-- BARIS 1 --}}
    <div class="back-row">
        <a href="{{ route('bagi_hasil') }}" class="btn-back">
           <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- BARIS 2 --}}
    <div class="action-row">

        <div class="action-left">
            <button class="btn btn-dark" onclick="openBukti()">
                Bukti Fee
            </button>

            <a href="{{ route('bagi_hasil',[
                'view' => 'riwayat',
                'cabang' => $cabangTerpilih->idcabang
            ]) }}"
            class="btn btn-riwayat">
                Riwayat Bagi Hasil
            </a>
        </div>

        <div class="action-right">
            <button class="btn btn-orange" onclick="openSkala()">
                Pengaturan Skala
            </button>
        </div>

    </div>

</div>

<div class="detail-wrapper">
    <div class="detail-card-custom">

        <div class="top-info-row">
            <div class="rekening-pill">
                Rekening Tujuan Fee:<br>
                Mandiri - 98767896540 <br>
                a.n OwnerOutdoorKriss
            </div>

            <div class="bulan-box">
                <label>Pilih Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan }}" id="bulanFilter">
            </div>
        </div>

        {{-- JUDUL --}}
        <h5 class="detail-title-center">
            Detail Perhitungan Bagi Hasil
        </h5>

        {{-- TOTAL --}}
        <div class="total-pill">
            <span>Total Pendapatan Cabang</span>
            <strong>
                Rp {{ number_format($totalPendapatan,0,',','.') }}
            </strong>
        </div>

        {{-- OWNER BOX --}}
        <div class="hasil-box">
            <div class="hasil-header">
                <strong>Owner</strong>
                <span><span id="cardPersenOwner">{{ (int) $persenOwner }}</span>%</span>
            </div>

            <div class="row-between">
                <span>Perhitungan:</span>
                <span>
                    Rp {{ number_format($totalPendapatan,0,',','.') }}
                    × {{ (int) $persenOwner }}%
                </span>
            </div>

            <hr>

            <div class="row-between">
                <strong>Hasil :</strong>
                <strong id="cardNominalOwner">
                    Rp {{ number_format($hasilOwner,0,',','.') }}
                </strong>
            </div>
        </div>

        {{-- ADMIN BOX --}}
        <div class="hasil-box">
            <div class="hasil-header">
                <strong>Admin Cabang</strong>
                <span><span id="cardPersenCabang">{{ (int) $persenCabang }}</span>%</span>
            </div>

             <div class="row-between">
                <span>Perhitungan:</span>
                <span>
                    Rp {{ number_format($totalPendapatan,0,',','.') }}
                    × {{ (int) $persenCabang }}%
                </span>
            </div>

            <hr>

            <div class="row-between">
                <strong>Hasil :</strong>
                <strong id="cardNominalCabang">
                    Rp {{ number_format($hasilCabang,0,',','.') }}
                </strong>
            </div>
        </div>

        {{-- BUTTON --}}
       <form action="{{ route('bagi_hasil.store') }}" method="POST">
    @csrf

    <input type="hidden" name="cabang_idcabang"
           value="{{ $cabangTerpilih->idcabang }}">
           
    <input type="hidden" name="bulan"
           value="{{ $bulan }}">

    <input type="hidden" name="nominal_owner" id="nominalOwner"
           value="{{ $hasilOwner }}">

    <input type="hidden" name="nominal_cabang" id="nominalCabang"
           value="{{ $hasilCabang }}">

    <input type="hidden" name="presentase_owner" value="{{ $persenOwner ?? 0 }}">
    <input type="hidden" name="presentase_cabang" value="{{ $persenCabang ?? 0 }}">

    <button class="btn-green-full">Simpan</button>
</form>
    </div>
</div>

@endif

    {{-- ================= RIWAYAT ================= --}}
   @if($view == 'riwayat')

<div class="top-action">
 <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Pencarian...">
    </div>
<a href="{{ route('bagi_hasil.detail', request('cabang')) }}"
   class="btn-back-red">
 Kembali <i class="fa-solid fa-arrow-right"></i>
</a>
</div>

<div class="riwayat-container">

<h3 class="riwayat-title">
Riwayat Bagi Hasil
</h3>

<table class="table-riwayat" id="dataTable">
    
<thead>
<tr>
<th>Bagi Hasil Bulan</th>
<th>Tanggal Upload</th>
<th>Total Pendapatan</th>
<th>Bagi Hasil Owner</th>
<th>Bagi Hasil Admin Cabang</th>
<th>Bukti Transfer</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@forelse($riwayat as $item)

<tr>
<td>
{{ \Carbon\Carbon::parse($item->bulan)->translatedFormat('F Y') }}
</td>

<td>
{{ \Carbon\Carbon::parse($item->created_at)->format('d F Y') }}
</td>

<td>
Rp {{ number_format($item->total_pendapatan ?? 0,0,',','.') }}
</td>

<td>
Rp {{ number_format($item->nominal_owner,0,',','.') }}
</td>

<td>
Rp {{ number_format($item->nominal_cabang,0,',','.') }}
</td>

<td>
@if($item->bukti_fee)
<a href="{{ asset('storage/'.$item->bukti_fee) }}" target="_blank" class="btn-eye">
<i class="fa-solid fa-eye"></i>
</a>
@else
-
@endif
</td>

<td class="status-text">
{{ ucfirst($item->status) }}
</td>

</tr>

@empty

<tr>
<td colspan="7" style="text-align:center;">
Belum ada riwayat bagi hasil
</td>
</tr>

@endforelse

</tbody>

</table>
@if(method_exists($riwayat, 'links'))
<div class="pagination-simple">

    {{-- Prev --}}
    @if ($riwayat->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $riwayat->previousPageUrl() }}" class="nav">«</a>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
        @if ($page == $riwayat->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if ($riwayat->hasMorePages())
        <a href="{{ $riwayat->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

</div>
@endif
</div>

@endif

@if($view == 'detail' && isset($cabangTerpilih))

{{-- ================= MODAL ================= --}}
<div class="modal-overlay" id="modalBukti">
    <div class="modal-box bukti-box">
        <span class="modal-close" onclick="closeBukti()">&times;</span>
        <h5 class="bukti-title">Bukti Fee</h5>

        <table class="bukti-table">
            <thead>
                <tr>
                    <th>Tanggal Upload</th>
                    <th>Bukti Transfer</th>
                    <th>Status</th>
                    <th>Konfirmasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buktiFee as $item)

<tr>

<td>
{{ \Carbon\Carbon::parse($item->updated_at)->format('d F Y') }}
</td>

<td>
<a href="{{ asset('storage/'.$item->bukti_fee) }}" target="_blank" class="lihat-bukti">
<i class="fa-solid fa-eye"></i>
Lihat Bukti
</a>
</td>

<td>
{{ ucfirst($item->status) }}
</td>

<td>

<div class="konfirmasi-btns">

@if($item->status == 'menunggu')

<form action="{{ route('bagi_hasil.konfirmasi',$item->idbagi_hasil) }}" method="POST">
@csrf
<button class="btn-bulat hijau">
<i class="fa-solid fa-check"></i>
</button>
</form>

<form action="{{ route('bagi_hasil.tolak',$item->idbagi_hasil) }}" method="POST">
@csrf
<button class="btn-bulat merah">
<i class="fa-solid fa-xmark"></i>
</button>
</form>

@elseif($item->status == 'terkonfirmasi')

<button class="btn-bulat hijau" disabled>
<i class="fa-solid fa-check"></i>
</button>

@elseif($item->status == 'ditolak')

<button class="btn-bulat merah" disabled>
<i class="fa-solid fa-xmark"></i>
</button>

@endif

</div>

</td>

</tr>

@endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalSkala">

<form action="{{ route('skala_bagi_hasil.store') }}"
      method="POST"
      class="modal-box">

@csrf
        <span class="modal-close" onclick="closeSkala()">&times;</span>

        <h4 class="modal-title">Pengaturan Skala</h4>
        <p class="skala-nb">NB : Jika Terjadi Perubahan MoU</p>
        <input type="hidden"
        name="cabang_idcabang"
        value="{{ $cabangTerpilih->idcabang }}">

        <input type="hidden"
            name="owner"
            id="ownerInput"
            value="{{ (int) $persenOwner }}">

        <div class="skala-wrapper">

            <div class="skala-box">
                <div class="skala-title">Owner</div>
                <div class="skala-percent">
                    <span id="ownerVal">{{ (int) $persenOwner }}</span>%
                </div>
            </div>

            <div class="skala-box">
                <div class="skala-title">Admin Cabang</div>
                <div class="skala-percent">
                    <span id="cabangVal">{{ (int) $persenCabang }}</span>%
                </div>
            </div>

        </div>

        <input type="range"
               class="skala-range"
               min="0"
               max="100"
               value="{{ (int) $persenOwner }}"
               oninput="updateRange(this.value)">

        <div class="modal-btn-group">
            <button type="submit" class="btn-save">Simpan</button>
            <button type="button" class="btn-cancel" onclick="closeSkala()">Batal</button>
        </div>
    </div>
</form>

@endif
<script>
function openBukti(){
    document.getElementById('modalBukti').style.display = 'flex';
}

function closeBukti(){
    document.getElementById('modalBukti').style.display = 'none';
}

function openSkala(){
    document.getElementById('modalSkala').style.display = 'flex';
}

function closeSkala(){
    document.getElementById('modalSkala').style.display = 'none';
}
function updateRange(val){

    let persenOwner = parseInt(val);
    let persenCabang = 100 - persenOwner;

    document.getElementById('ownerVal').innerText = persenOwner;
    document.getElementById('cabangVal').innerText = persenCabang;

    document.getElementById('cardPersenOwner').innerText = persenOwner;
    document.getElementById('cardPersenCabang').innerText = persenCabang;

    document.getElementById('ownerInput').value = persenOwner;

    let total = {{ $totalPendapatan ?? 0 }};

    let hasilOwner = Math.round(total * persenOwner / 100);
    let hasilCabang = Math.round(total * persenCabang / 100);

    document.getElementById('nominalOwner').value = hasilOwner;
    document.getElementById('nominalCabang').value = hasilCabang;

    // update tampilan rupiah realtime
    document.getElementById('cardNominalOwner').innerText =
        "Rp " + hasilOwner.toLocaleString('id-ID');

    document.getElementById('cardNominalCabang').innerText =
        "Rp " + hasilCabang.toLocaleString('id-ID');
}
const bulanFilter = document.getElementById('bulanFilter');

if (bulanFilter) {
    bulanFilter.addEventListener('change', function () {
        let bulan = this.value;

        let url = new URL(window.location.href);
        url.searchParams.set('bulan', bulan);

        window.location.href = url.toString();
    });
}
</script>
@if($view == 'riwayat')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('searchInput');
    const table = document.getElementById('dataTable');

    if (!input || !table) return;

    input.addEventListener('input', function () {

        const value = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });

    });

});
</script>
@endif

@endsection