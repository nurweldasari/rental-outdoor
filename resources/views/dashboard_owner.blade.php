@extends('layouts.app')

@php
    $active = 'dashboard';
@endphp

@section('title','Dashboard Owner')

@section('content')
<form method="GET" class="filter">
  <input type="hidden" name="tahun_pendapatan" value="{{ $tahunPendapatan }}">
  <input type="hidden" name="bulan_pendapatan" value="{{ $bulanPendapatan }}">
  @php
    $currentYear = date('Y');
@endphp

<select name="tahun_card" onchange="this.form.submit()">
    <option value="">Pilih Tahun</option>

    @for ($i = $currentYear + 10; $i >= $currentYear - 5; $i--)
        <option value="{{ $i }}" {{ ($tahunCard ?? '') == $i ? 'selected' : '' }}>
            {{ $i }}
        </option>
    @endfor
</select>
</form>
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

      <form method="GET">

    {{-- biar filter card tetap kebawa --}}
    <input type="hidden" name="tahun_card" value="{{ $tahunCard }}">

    <div class="periode">
        <span>Pilih Bulan</span>

        <select name="bulan_pendapatan" onchange="this.form.submit()">

            <option value="">Semua Bulan</option>

            @for($i=1; $i<=12; $i++)
                <option value="{{ $i }}"
                    {{ $bulanPendapatan == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
            @endfor

        </select>
    </div>

    <div class="periode">
        <span>Pilih Tahun</span>

        <select name="tahun_pendapatan" onchange="this.form.submit()">

            @for($y=date('Y'); $y>=2023; $y--)
                <option value="{{ $y }}"
                    {{ $tahunPendapatan == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor

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
        <div class="tahun">{{ $tahunCard }}</div>
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