@extends('layouts.app')

@php
    $active = 'penyewaan';
@endphp

@section('title','Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_penyewaan.css') }}">
@endpush 

@section('content')
<div class="container-data">

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="left">
            <form id="filterForm" method="GET">
                <select class="per-page" name="per_page" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page')==10?'selected':'' }}>10</option>
                    <option value="25" {{ request('per_page')==25?'selected':'' }}>25</option>
                    <option value="50" {{ request('per_page')==50?'selected':'' }}>50</option>
                    <option value="100" {{ request('per_page')==100?'selected':'' }}>100</option>
                </select>
                <span>Data Per Halaman</span>

                <input type="text" class="search" name="search" placeholder="Pencarian..." value="{{ request('search') }}">
            </form>
        </div>
    </div>

    <!-- HEADER -->
    <div class="table-header">
        <div>No.</div>
        <div>Tanggal Reservasi</div>
        <div>Penyewa</div>
        <div>Total</div>
        <div>Status</div>
        <div>Konfirmasi</div>
        <div>Aksi</div>
    </div>

    <!-- ROW -->
    @forelse($penyewaanList as $i => $p)
        <div class="table-row">
            <div>{{ $penyewaanList->firstItem() + $i }}.</div>
            <div>{{ \Carbon\Carbon::parse($p->tanggal_sewa)->translatedFormat('l, d M Y') }}</div>
            <div>
    <strong>{{ optional($p->penyewa->user)->nama ?? '-' }}</strong>
    ({{ optional($p->penyewa->user)->no_telepon ?? '-' }})
</div>


            <div>Rp {{ number_format($p->total) }}</div>
            <div>
                @php
                    $statusClass = match($p->status_penyewaan) {
                        'menunggu_pembayaran' => 'pending',
                        'sedang_disewa'       => 'active',
                        'selesai'             => 'done',
                        'dibatalkan'          => 'cancel',
                        default               => ''
                    };
                    $statusText = match($p->status_penyewaan) {
                        'menunggu_pembayaran' => 'Menunggu',
                        'sedang_disewa'       => 'Sedang Disewa',
                        'selesai'             => 'Selesai',
                        'dibatalkan'          => 'Dibatalkan',
                        default               => '-'
                    };
                @endphp
                <span class="status {{ $statusClass }}">{{ $statusText }}</span>
            </div>

            <div class="confirm">
@if($p->status_penyewaan === 'menunggu_pembayaran')
    <!-- Tombol aktif untuk konfirmasi dan cancel -->
    <button 
        class="icon ok" 
        data-url="{{ route('admin.konfirmasi_bayar', $p->idpenyewaan) }}"
    >✔</button>

    <button 
        class="icon cancel" 
        data-url="{{ route('admin.penyewaan.cancel', $p->idpenyewaan) }}"
    >✖</button>

@elseif($p->status_penyewaan === 'sedang_disewa')
    <!-- Hanya ikon ✔ non-klik -->
    <span class="icon ok">✔</span>

@elseif($p->status_penyewaan === 'dibatalkan')
    <!-- Hanya ikon ❌ non-klik -->
    <span class="icon cancel">✖</span>

@elseif($p->status_penyewaan === 'selesai')
    <!-- Hanya ikon ✔ non-klik -->
    <span class="icon ok">✔</span>
@endif
</div>
            <div>
                <div>
    <a href="{{ route('admin.penyewaan.detail', $p->idpenyewaan) }}" 
       class="btn-detail">
        Detail
    </a>
</div>

            </div>
        </div>
    @empty
        <p style="text-align:center;margin-top:40px;">Belum ada riwayat penyewaan</p>
    @endforelse

  <div class="pagination-simple">
    {{-- Prev --}}
    @if ($penyewaanList->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $penyewaanList->previousPageUrl() }}" class="nav">«</a>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($penyewaanList->getUrlRange(1, $penyewaanList->lastPage()) as $page => $url)
        @if ($page == $penyewaanList->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if ($penyewaanList->hasMorePages())
        <a href="{{ $penyewaanList->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif
</div>



</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // Hanya tombol OK untuk menunggu
    document.querySelectorAll('.icon.ok[data-url]').forEach(el => {
        el.addEventListener('click', function() {
            const url = this.dataset.url;
            const row = this.closest('.table-row');
            const cancelEl = row.querySelector('.icon.cancel');
            const statusEl = row.querySelector('.status');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(res => res.json())
              .then(data => {
                  if(data.success){
                      statusEl.textContent = 'Sedang Disewa';
                      statusEl.className = 'status active';
                      // hilangkan tombol cancel
                      if(cancelEl) cancelEl.remove();
                      // ganti tombol ok menjadi span supaya tidak bisa diklik lagi
                      this.outerHTML = '<span class="icon ok">✔</span>';
                  } else {
                      alert(data.message || 'Gagal konfirmasi');
                  }
              });
        });
    });

    // Hanya tombol Cancel untuk menunggu
    document.querySelectorAll('.icon.cancel[data-url]').forEach(el => {
        el.addEventListener('click', function() {
            const url = this.dataset.url;
            const row = this.closest('.table-row');
            const okEl = row.querySelector('.icon.ok');
            const statusEl = row.querySelector('.status');

            if(confirm('Yakin ingin membatalkan penyewaan ini?')) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                  .then(data => {
                      if(data.success){
                          statusEl.textContent = 'Dibatalkan';
                          statusEl.className = 'status cancel';
                          // hilangkan tombol ok
                          if(okEl) okEl.remove();
                          // ganti tombol cancel menjadi span
                          this.outerHTML = '<span class="icon cancel">✖</span>';
                      } else {
                          alert(data.message || 'Gagal membatalkan');
                      }
                  });
            }
        });
    });

});

</script>

@endpush
