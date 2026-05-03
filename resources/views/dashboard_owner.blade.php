@extends('layouts.app')

@php
    $active = 'dashboard';
@endphp

@section('title','Dashboard Owner')

@section('content')

<div class="content-wrapper">

  <!-- ================= CARDS ================= -->
  <section class="cards">

    <div class="card">
      <i class="fa-solid fa-user"></i>
      <h2>{{ $totalCabang ?? 0 }}</h2>
      <p>Total Cabang</p>
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


  <!-- ================= BOTTOM ================= -->
  <section class="dashboard-bottom">

    <!-- ================= LEFT (PENDAPATAN) ================= -->
    <div class="pendapatan-box">

      <h3>Pendapatan</h3>

      <div class="periode">
        <span>Periode</span>
        <select>
          <option>Bulanan</option>
        </select>
      </div>

      <form method="GET">

  <div class="periode">
    <span>Pilih Tahun</span>
    <select name="tahun" onchange="this.form.submit()">
      <option value="2024" {{ $tahun == 2024 ? 'selected' : '' }}>2024</option>
      <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
      <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
    </select>
  </div>

</form>

      @forelse($pendapatanList as $p)
        <div class="progress-item">

          <div class="label">
            <span>{{ $p['nama'] }}</span>
            <span>Rp {{ number_format($p['total'],0,',','.') }}</span>
          </div>

          <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $p['persen'] }}%"></div>
          </div>

        </div>
      @empty
        <p style="text-align:center;">Belum ada data</p>
      @endforelse

    </div>


    <!-- ================= RIGHT (DONUT) ================= -->
    <div class="sewa-box">

      <div class="sewa-header">
        <h3>Alat Banyak Disewa</h3>
        <div class="tahun">{{ $tahun }}</div>
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

</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const dataAlat = @json($alatPersen ?? []);

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
                    borderColor: '#e9d4c3',
                    borderWidth: 6
                }]
            },
            options: {
                cutout: '72%',
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