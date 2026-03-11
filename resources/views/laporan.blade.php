@extends('layouts.app')

@php $active = 'laporan'; @endphp

@section('title','Laporan Pendapatan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@section('content')
<div class="laporan-container">
     {{-- ================= HEADER ================= --}}

    {{-- OWNER (Tampil di layar) --}}
    @if(auth()->user()->status === 'owner')
        <div class="no-print">
            <a href="{{ route('laporan') }}" class="btn-kembali">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>

            <h2>
                Laporan Pendapatan - {{ $cabang->nama_cabang ?? '' }}
            </h2>
            <div class="print-date-range" id="printDateRange"></div>
        </div>
    @endif

    {{-- ADMIN CABANG (Hanya muncul saat print) --}}
    @if(auth()->user()->status === 'admin_cabang')
        <h2 class="print-only">Laporan Pendapatan</h2>
    @endif


    {{-- ================= TOP SECTION ================= --}}
    <div class="laporan-top">

        {{-- PILIH BULAN (FILTER VIEW) --}}
        <form method="GET" action="{{ route('laporan') }}">
            <input type="hidden" name="cabang" value="{{ $cabang->idcabang }}">
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
                {{ \Carbon\Carbon::parse($data->tanggal_sewa)->translatedFormat('d M Y') }}
            </div>

            <div>
                <strong>{{ $data->penyewa->user->nama }}</strong><br>
                <small>({{ $data->penyewa->user->no_telepon }})</small>
            </div>

            <div class="produk-list">
                @foreach($data->itemPenyewaan as $item)
                    {{ $loop->iteration }}.
                    {{ $item->produk->nama_produk }}
                    ({{ $item->qty }}) <br>
                @endforeach
            </div>

            <div>
                Rp {{ number_format($data->total,0,',','.') }}
            </div>
        </div>
        @endforeach

        @if($penyewaan->count())
        <div class="laporan-total">
            <div></div>
            <div></div>
            <div></div>
            <div class="total-label">Total Pendapatan</div>
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
    document.getElementById('printDateRange').textContent = `Dari ${dariFormatted} sampai ${sampaiFormatted}`;

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