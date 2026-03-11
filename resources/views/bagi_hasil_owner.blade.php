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

            <a href="{{ route('bagi_hasil',['view'=>'riwayat']) }}"
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

        {{-- REKENING --}}
        <div class="rekening-pill">
            Rekening Tujuan Fee:<br>
            Mandiri - 98767896540 <br>
            a.n OwnerOutdoorKriss
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
                <span><span id="cardPersenOwner">{{ $persenOwner }}</span>%</span>
            </div>

            <div class="row-between">
                <span>Perhitungan:</span>
                <span>
                    Rp {{ number_format($totalPendapatan,0,',','.') }}
                    × {{ $persenOwner }}%
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
                <span><span id="cardPersenCabang">{{ $persenCabang }}</span>%</span>
            </div>

             <div class="row-between">
                <span>Perhitungan:</span>
                <span>
                    Rp {{ number_format($totalPendapatan,0,',','.') }}
                    × {{ $persenCabang }}%
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

    <input type="hidden" name="presentase_owner" id="inputOwner"
           value="{{ $persenOwner }}">

    <input type="hidden" name="presentase_cabang" id="inputCabang"
           value="{{ $persenCabang }}">

    <input type="hidden" name="nominal_owner" id="nominalOwner"
           value="{{ $hasilOwner }}">

    <input type="hidden" name="nominal_cabang" id="nominalCabang"
           value="{{ $hasilCabang }}">

    <button class="btn-green-full">Simpan</button>
</form>
    </div>
</div>

@endif

    {{-- ================= RIWAYAT ================= --}}
   @if($view == 'riwayat')

<div class="top-action">
<a href="{{ route('bagi_hasil') }}" class="btn-back-red">
<i class="fa-solid fa-arrow-left"></i> Kembali
</a>
</div>

<div class="riwayat-container">

<h3 class="riwayat-title">
Riwayat Bagi Hasil
</h3>

<table class="table-riwayat">

<thead>
<tr>
<th>Tanggal Upload</th>
<th>Total Pendapatan</th>
<th>Bagi Hasil Owner</th>
<th>Bukti Transfer</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@forelse($riwayat as $item)

<tr>

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
<td colspan="5" style="text-align:center;">
Belum ada riwayat bagi hasil
</td>
</tr>

@endforelse

</tbody>

</table>

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

</div>

</td>

</tr>

@endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalSkala">
    <div class="modal-box">
        <span class="modal-close" onclick="closeSkala()">&times;</span>

        <h4 class="modal-title">Pengaturan Skala</h4>
        <p class="skala-nb">NB : Jika Terjadi Perubahan MoU</p>

        <div class="skala-wrapper">

            <div class="skala-box">
                <div class="skala-title">Owner</div>
                <div class="skala-percent">
                    <span id="ownerVal">{{ $persenOwner }}</span>%
                </div>
            </div>

            <div class="skala-box">
                <div class="skala-title">Admin Cabang</div>
                <div class="skala-percent">
                    <span id="cabangVal">{{ $persenCabang }}</span>%
                </div>
            </div>

        </div>

        <input type="range"
               class="skala-range"
               min="0"
               max="100"
               value="{{ $persenOwner }}"
               oninput="updateRange(this.value)">

        <div class="modal-btn-group">
            <button class="btn-save" onclick="closeSkala()">Simpan</button>
            <button class="btn-cancel" onclick="closeSkala()">Batal</button>
        </div>
    </div>
</div>

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

    document.getElementById('inputOwner').value = persenOwner;
    document.getElementById('inputCabang').value = persenCabang;

    let total = {{ $totalPendapatan ?? 0 }};

    let hasilOwner = total * persenOwner / 100;
    let hasilCabang = total * persenCabang / 100;

    document.getElementById('nominalOwner').value = hasilOwner;
    document.getElementById('nominalCabang').value = hasilCabang;

    // update tampilan rupiah realtime
    document.getElementById('cardNominalOwner').innerText =
        "Rp " + hasilOwner.toLocaleString('id-ID');

    document.getElementById('cardNominalCabang').innerText =
        "Rp " + hasilCabang.toLocaleString('id-ID');
}
</script>

@endsection