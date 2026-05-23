@extends('layouts.app')

@php $active = 'laporan_pendapatan'; @endphp

@section('title','Laporan Pendapatan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@section('content')
<div class="laporan-container">
     {{-- ================= HEADER ================= --}}

    <div class="no-print">

    <h2>
        Laporan Pendapatan - {{ $cabang->nama_cabang ?? 'Pusat' }}
    </h2>
</div>

{{-- INI WAJIB ADA UNTUK SEMUA ROLE --}}
<div class="print-date-range" id="printDateRange"></div>


    {{-- ================= TOP SECTION ================= --}}
    <div class="laporan-top">

        {{-- PILIH BULAN (FILTER VIEW) --}}
        <form method="GET" action="{{ route('laporan_pusat') }}">
            
            <input type="month"
                name="bulan"
                class="filter-box"
                value="{{ request('bulan') }}"
                onchange="this.form.submit()">
        </form>

        {{-- CETAK --}}
        @if($penyewaan->count())
            <button type="button" class="btn-submit" onclick="openModal()">
                Cetak Laporan
            </button>
        @endif

    </div>


    {{-- ================= MODAL CETAK ================= --}}
    <div class="modal-overlay" id="modalCetak">
        <div class="modal-box">
            <span class="modal-close" onclick="closeModal()">&times;</span>

            <h3>Cetak Laporan</h3>

            <form onsubmit="return handlePrint(event)">

                <div class="form-group">
                    <label>Dari</label>
                    <input type="date" name="dari">
                </div>

                <div class="form-group">
                    <label>Sampai</label>
                    <input type="date" name="sampai">
                </div>

                <div class="modal-action">
                    <button type="submit" class="btn-cetak">
                        <i class="fa-solid fa-print"></i>Cetak
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ================= TABEL ================= --}}
    <div class="laporan-table">

        <div class="laporan-header">
            <div>No.</div>
            <div>Tanggal Sewa</div>
            <div>Penyewa</div>
            <div>Item Produk</div>
            <div>Total</div>
        </div>

        @if($penyewaan->isEmpty())
            <div style="padding:40px;text-align:center;color:#888;">
                Laporan belum ada
            </div>
        @endif

        @php $no = 1; @endphp

        @foreach($penyewaan as $data)
        <div class="laporan-row" data-date="{{ $data->tanggal_sewa }}">
            <div>{{ $no++ }}</div>

            <div>
                {{ \Carbon\Carbon::parse($data->tanggal_sewa)->translatedFormat('l, d M Y') }}
            </div>

            <div class="user-info">
    <strong>{{ $data->penyewa->user->nama }}</strong>
    <small>({{ $data->penyewa->user->no_telepon }})</small>
</div>

<div class="produk-list">

    @foreach($data->itemPenyewaan as $item)

        {{-- ================= PRODUK ================= --}}
        @if($item->type === 'produk')

            <div class="produk-item">

                <span class="produk-nama">
                    {{ $loop->iteration }}.
                    {{ $item->nama_produk ?? '-' }}
                    ({{ $item->qty }})
                </span>

            </div>

        {{-- ================= PAKET ================= --}}
        @elseif($item->type === 'paket')

            <div class="produk-item">

                <div class="paket-nama">
                    {{ $loop->iteration }}.
                    <strong>{{ $item->nama_paket ?? '-' }}</strong>
                    ({{ $item->qty }})
                </div>

                @php
                    $detail = json_decode($item->detail_paket ?? '[]', true);
                @endphp

                <div class="paket-detail">

                    @foreach($detail as $d)

                        <div class="paket-detail-item">
                            • {{ $d['nama_produk'] ?? '-' }}
                            ({{ $d['qty'] ?? 0 }})
                        </div>

                    @endforeach

                </div>

            </div>

        @endif

    @endforeach

</div>

{{-- TOTAL --}}
<div class="harga-total">
    Rp {{ number_format($data->total,0,',','.') }}
</div>

</div>
@endforeach

@if($penyewaan->count())

<div class="laporan-total">

    <div></div>
    <div></div>
    <div></div>

    <div class="total-label">
        Total Pendapatan
    </div>

    <div class="total-value">
        Rp {{ number_format($totalPendapatan,0,',','.') }}
    </div>

</div>

@endif

</div>
</div>
{{-- ================= SCRIPT ================= --}}
<script>
function openModal() {
    document.getElementById('modalCetak').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalCetak').style.display = 'none';
}

function handlePrint(e) {
    e.preventDefault();

    const dari = document.querySelector('input[name="dari"]').value;
    const sampai = document.querySelector('input[name="sampai"]').value;

    if (!dari || !sampai) {
        alert("Isi tanggal dulu.");
        return false;
    }

    // Format tanggal ke "Rabu, 01 Jan 2025"
    const options = { weekday: 'long', day: '2-digit', month: 'short', year: 'numeric' };
    const dariFormatted = new Date(dari).toLocaleDateString('id-ID', options);
    const sampaiFormatted = new Date(sampai).toLocaleDateString('id-ID', options);

    // Set ke elemen printDateRange
   const el = document.getElementById('printDateRange');
if (el) {
    el.textContent = `Dari ${dariFormatted} sampai ${sampaiFormatted}`;
}
    // Sembunyikan row yang di luar range
    const d1 = new Date(dari);
    const d2 = new Date(sampai);

    document.querySelectorAll(".laporan-row").forEach(row => {
        const tanggal = new Date(row.getAttribute("data-date"));
        if (tanggal < d1 || tanggal > d2) {
            row.classList.add("hidden-print");
        }
    });

    window.onafterprint = function () {
        location.reload(); // balikin semua row setelah print
    };

    window.print();
}
</script>
@endsection