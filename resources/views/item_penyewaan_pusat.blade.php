@extends('layouts.app')

@php
    $active = 'penyewaan';
@endphp

@section('title','Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item_penyewaan.css') }}">
@endpush 

@section('content')
<div class="container-riwayat">

    <!-- TAB HEADER -->
    <div class="tab-header">
        <button class="tab active" data-tab="belum-bayar">Belum Bayar</button>
        <button class="tab" data-tab="penyewaan">Penyewaan</button>
    </div>

    <!-- ================= TAB BELUM BAYAR ================= -->
    <div class="tab-content active" id="belum-bayar">

        @forelse ($belumBayar as $item)
            @php
                $sisaDetik = now()->diffInSeconds($item->batas_pembayaran, false);
            @endphp

            <div class="card-riwayat">
                <div class="card-left">
                    <p><strong>Nama</strong> : {{ auth()->user()->nama }}</p>
                    <p><strong>No. Telephone</strong> : {{ auth()->user()->no_telepon ?? '-' }}</p>
                    <p>
                        <strong>Tanggal Sewa</strong> :
                        {{ \Carbon\Carbon::parse($item->tanggal_sewa)->translatedFormat('d F Y') }}
                    </p>
                </div>

                <div class="divider"></div>

                <div class="card-right">
                    <span class="timer" data-time="{{ $sisaDetik }}">
                        sisa waktu<br>
                        <strong class="countdown">-- : -- : --</strong>
                    </span>

                    <p>Metode Pembayaran :
                        <strong>{{ ucfirst($item->metode_bayar) }}</strong>
                    </p>

                    <p class="total">
                        Total : Rp {{ number_format($item->total,0,',','.') }}
                    </p>

                    <div class="action">
                        @if ($item->metode_bayar === 'transfer')
                            @if ($item->bukti_bayar)
                                <button class="btn waiting">
                                    <i class="fa-solid fa-clock-rotate-left" style="color: brown; margin-right: 5px;"></i>
                                    Menunggu Konfirmasi Admin</button>
                            @else
                                <a href="{{ route('penyewaan_pusat.upload_pembayaran', $item->idpenyewaan) }}"
                                   class="btn upload">
                                    <i class="fa-solid fa-cloud-arrow-up" style="color: white; margin-right: 5px;"></i>
                                    Upload Bukti Transfer
                                </a>
                            @endif
                        @else
                            <button class="btn waiting">
                            <i class="fa-solid fa-clock-rotate-left" style="color: brown; margin-right: 5px;"></i>    
                            Menunggu Pembayaran di toko</button>
                        @endif

                        <a href="{{ route('detail_sewa_pusat', $item->idpenyewaan) }}"
                           class="btn detail">
                           <i class="fa-solid fa-circle-info" style="color: white; margin-right: 5px;"></i>
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="empty">Tidak ada penyewaan yang belum dibayar</p>
        @endforelse
<!-- ================= PAGINATION ================= -->
<div class="pagination-simple">

    @if ($belumBayar->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $belumBayar->previousPageUrl() }}" class="nav">«</a>
    @endif

    @foreach ($belumBayar->getUrlRange(1, $belumBayar->lastPage()) as $page => $url)
        @if ($page == $belumBayar->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    @if ($belumBayar->hasMorePages())
        <a href="{{ $belumBayar->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

</div>
    </div>

    <!-- ================= TAB PENYEWAAN ================= -->
    <div class="tab-content" id="penyewaan">

        @forelse ($penyewaanAktif as $item)
            <div class="card-riwayat aktif">
                <div class="card-left">
                    <p><strong>Nama</strong> : {{ auth()->user()->nama }}</p>
                    <p><strong>No. Telephone</strong> : {{ auth()->user()->no_telepon ?? '-' }}</p>
                    <p>
                        <strong>Tanggal Sewa</strong> :
                        {{ \Carbon\Carbon::parse($item->tanggal_sewa)->translatedFormat('d F Y') }}
                    </p>
                </div>

                <div class="divider"></div>

                <div class="card-right">

    <div class="status-wrapper">
        <div class="badge-disewa">Sedang Disewa</div>
    </div>

    <p>Metode Pembayaran :
        <strong>{{ ucfirst($item->metode_bayar) }}</strong>
    </p>

    <p class="total">
        Total : Rp {{ number_format($item->total,0,',','.') }}
    </p>

    <div class="action">
        <a href="{{ route('struk', $item->idpenyewaan) }}"
           class="btn upload"
           target="_blank">
           <i class="fa-solid fa-print" style="color: white; margin-right: 5px;"></i>
            Cetak Struk
        </a>

        <a href="{{ route('detail_sewa_pusat', $item->idpenyewaan) }}"
           class="btn detail">
           <i class="fa-solid fa-circle-info" style="color: white; margin-right: 5px;"></i>
            Detail
        </a>
    </div>

</div>
            </div>
        @empty
            <p class="empty">Tidak ada penyewaan aktif</p>
        @endforelse
<!-- ================= PAGINATION ================= -->
<div class="pagination-simple">

    @if ($belumBayar->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $belumBayar->previousPageUrl() }}" class="nav">«</a>
    @endif

    @foreach ($belumBayar->getUrlRange(1, $belumBayar->lastPage()) as $page => $url)
        @if ($page == $belumBayar->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    @if ($belumBayar->hasMorePages())
        <a href="{{ $belumBayar->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

</div>
    </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
/* ================= TAB FUNCTION ================= */
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function () {

        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});

/* ================= COUNTDOWN TIMER ================= */
document.querySelectorAll('.timer').forEach(timer => {
    let sisa = parseInt(timer.dataset.time);
    const el = timer.querySelector('.countdown');

    function tick() {
        if (sisa <= 0) {
            el.innerText = '00 : 00 : 00';
            return;
        }

        let h = Math.floor(sisa / 3600);
        let m = Math.floor((sisa % 3600) / 60);
        let s = sisa % 60;

        el.innerText =
            String(h).padStart(2,'0') + ' : ' +
            String(m).padStart(2,'0') + ' : ' +
            String(s).padStart(2,'0');

        sisa--;
    }

    tick();
    setInterval(tick, 1000);
});
</script>
@endpush
