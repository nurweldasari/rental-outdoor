@extends('layouts.app')

@php
    $active = 'dashboard';
@endphp

@section('title','Dashboard')

@section('content')

<form method="GET" class="filter">
  <select name="tahun" onchange="this.form.submit()">
    <option value="">Pilih Tahun</option>
    <option value="2024" {{ ($tahun ?? '') == 2024 ? 'selected' : '' }}>2024</option>
    <option value="2025" {{ ($tahun ?? '') == 2025 ? 'selected' : '' }}>2025</option>
  </select>
</div>

<section class="cards">

  <div class="card">
    <i class="fa-solid fa-user"></i>
    <h2>{{ $totalPenyewa ?? 0 }}</h2>
    <p>Total Penyewa</p>
  </div>

  <div class="card">
    <i class="fa-solid fa-cart-shopping"></i>
    <h2>{{ $totalPenyewaan ?? 0 }}</h2>
    <p>Total Penyewaan</p>
  </div>

  <div class="card">
    <i class="fa-solid fa-store"></i>
    <h2>{{ $totalAlat ?? 0 }}</h2>
    <p>Total Alat</p>
  </div>

  <div class="card">
    <i class="fa-solid fa-layer-group"></i>
    <h2>{{ $totalKategori ?? 0 }}</h2>
    <p>Kategori</p>
  </div>

</section>

<section class="dashboard-bottom">

  <!-- LEFT -->
  <div class="pendapatan-box">
    <h3>Pendapatan</h3>

    <div class="chart-wrapper">
      <canvas id="pendapatanChart"></canvas>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="sewa-box">

    <div class="sewa-header">
      <h3>Alat Banyak Disewa</h3>
      <div class="tahun">{{ $tahun ?? date('Y') }}</div>
    </div>

    <div class="donut-wrapper">
      <canvas id="alatChart"></canvas>
    </div>

    <div class="legend">
  @forelse($alatPersen ?? [] as $i => $a)
    <div class="item">
      <div class="left">
        <span class="dot" style="background: {{ ['#8b3f00','#d97706','#fbbf24'][$i] ?? '#ccc' }}"></span>
        <span class="nama">{{ $a['nama'] }}</span>
      </div>
      <span class="persen">{{ $a['persen'] }}%</span>
    </div>
  @empty
    <p style="text-align:center;">Belum ada data</p>
  @endforelse
</div>

  </div>

</section>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const dataPendapatan = @json($dataBulanan ?? []);
    const dataAlat = @json($alatPersen ?? []);

    /* ===== BAR CHART ===== */
    const ctx = document.getElementById('pendapatanChart');

    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGS','SEP','OKT','NOV','DES'],
                datasets: [{
                    data: dataPendapatan,
                    backgroundColor: '#8b3f00',
                    borderRadius: 10,
                    barThickness: 18
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }},
                scales: {
                    x: { grid: { display: false }},
                    y: {
                        grid: { display: false },
                        ticks: {
                            callback: v => 'Rp ' + Number(v).toLocaleString()
                        }
                    }
                }
            }
        });
    }

    /* ===== DONUT CHART (SUDAH DIPERBAIKI) ===== */
    const donut = document.getElementById('alatChart');

    if (donut) {

        const colors = ['#8b3f00','#d97706','#fbbf24'];

        new Chart(donut, {
            type: 'doughnut',
            data: {
                labels: dataAlat.map(a => a.nama),
                datasets: [{
                    data: dataAlat.map(a => a.persen),
                    backgroundColor: colors,
                    borderColor: '#e9d4c3', // gap antar slice
                    borderWidth: 6,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '72%', // bikin ring tipis seperti desain
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });

    }

});
</script>
@endpush