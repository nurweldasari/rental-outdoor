@extends('layouts.app')

@php $active = 'laporan_cabang'; @endphp

@section('title','Laporan Cabang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/laporan_cabang.css') }}">
@endpush

@section('content')

{{-- ================== MODE LIST CABANG ================== --}}
@if(isset($cabangList))
<div class="container-cabang">
    @foreach ($cabangList as $c)
    <div class="cabang-card">
        <i class="fa-solid fa-location-dot"></i>
        <div class="card-title">{{ $c->nama_cabang }}</div>
        <a href="{{ route('laporan') }}?cabang={{ $c->idcabang }}" class="btn-lihat">
            Lihat Laporan
        </a>
    </div>
    @endforeach
</div>

{{-- ================== MODE DETAIL LAPORAN ================== --}}
@elseif(isset($cabang))
<div class="laporan-container">

    @if(auth()->user()->status === 'owner')
        <a href="{{ route('laporan') }}" class="btn-kembali">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    @endif

    <h2>Laporan Pendapatan</h2>

    <div class="laporan-toolbar">
        {{-- Filter Periode --}}
        <form method="GET" class="filter-form">
            <div class="periode-box">
                <span id="periodeText">
                    {{ request('periode') 
                        ? \Carbon\Carbon::createFromFormat('Y-m', request('periode'))->translatedFormat('F Y') 
                        : 'Pilih Periode' }}
                </span>
                <i class="fa-solid fa-calendar"></i>
                <input type="month" name="periode" id="periodeInput" value="{{ request('periode') }}">
            </div>
        </form>

        <button type="button" class="btn-cetak" onclick="openModal()">
            Cetak Laporan
        </button>
    </div>

    {{-- Tabel Laporan --}}
    <div class="laporan-table">
        <div class="laporan-header">
            <div>No.</div>
            <div>Tanggal</div>
            <div>Penyewa</div>
            <div>Item</div>
            <div>Total</div>
        </div>

        @php $no = 1; @endphp
        @forelse($penyewaan as $data)
        <div class="laporan-row">
            <div>{{ $no++ }}</div>
            <div>{{ \Carbon\Carbon::parse($data->tanggal_sewa)->translatedFormat('d M Y') }}</div>
            <div>{{ $data->penyewa->user->nama ?? '-' }}</div>
            <div>
                @foreach($data->itemPenyewaan as $item)
                    {{ $item->produk->nama_produk ?? '-' }} ({{ $item->qty }})<br>
                @endforeach
            </div>
            <div>Rp {{ number_format($data->total,0,',','.') }}</div>
        </div>
        @empty
        <div class="laporan-row">
            <div colspan="5">Tidak ada data</div>
        </div>
        @endforelse

        <div class="laporan-total">
            <div></div><div></div><div></div>
            <div><strong>Total Pendapatan</strong></div>
            <div><strong>Rp {{ number_format($totalPendapatan ?? 0,0,',','.') }}</strong></div>
        </div>
    </div>
</div>

{{-- ================== MODAL CETAK ================== --}}
<div class="modal-overlay" id="modalCetak">
    <div class="modal-box">
        <h3>Cetak Laporan</h3>
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <form method="GET">
            <div class="form-group">
                <label>Dari</label>
                <input type="date" name="dari" required>
            </div>
            <div class="form-group">
                <label>Sampai</label>
                <input type="date" name="sampai" required>
            </div>
            <div class="modal-action">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-print"></i> Cetak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================== AREA PRINT ================== --}}
@if(request()->filled('dari') && request()->filled('sampai'))
<div class="print-area">
    <div class="print-header">
        <h2>LAPORAN PENDAPATAN PENYEWAAN</h2>
        <p>Dari {{ \Carbon\Carbon::parse(request('dari'))->translatedFormat('l, d F Y') }}</p>
        <p>Sampai {{ \Carbon\Carbon::parse(request('sampai'))->translatedFormat('l, d F Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Sewa</th>
                <th>Penyewa</th>
                <th>Alat</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($penyewaan as $data)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ \Carbon\Carbon::parse($data->tanggal_sewa)->translatedFormat('d F Y') }}</td>
                <td>{{ $data->penyewa->user->nama ?? '-' }}</td>
                <td>
                    @foreach($data->itemPenyewaan as $item)
                        {{ $item->produk->nama_produk ?? '-' }} ({{ $item->qty }})<br>
                    @endforeach
                </td>
                <td>Rp {{ number_format($data->total,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;"><strong>Total Pendapatan</strong></td>
                <td><strong>Rp {{ number_format($totalPendapatan ?? 0,0,',','.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
<script>
window.onload = function(){ window.print(); }
</script>
@endif

@endif {{-- <- menutup @elseif(isset($cabang)) --}}

@endsection

{{-- ================== SCRIPT ================== --}}
@push('scripts')
<script>
const input = document.getElementById("periodeInput");
const text = document.getElementById("periodeText");

const bulanNama = [
    "Januari","Februari","Maret","April","Mei","Juni",
    "Juli","Agustus","September","Oktober","November","Desember"
];

if(input){
    input.addEventListener("change", function(){
        if(this.value){
            const [tahun, bulan] = this.value.split("-");
            text.innerText = bulanNama[parseInt(bulan) - 1] + " " + tahun;
            this.form.submit();
        }
    });
}

function openModal(){ document.getElementById('modalCetak').style.display = 'flex'; }
function closeModal(){ document.getElementById('modalCetak').style.display = 'none'; }
window.onclick = function(e){
    const modal = document.getElementById('modalCetak');
    if(e.target === modal){ closeModal(); }
}
</script>
@endpush