<!DOCTYPE html>
<html>
<head>
<title>Struk Penyewaan</title>
<link rel="stylesheet" href="{{ asset('css/struk.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="wrapper">

<div class="receipt">

<!-- HEADER -->
<div class="header">
    <img src="{{ asset('assets/images/logo.png') }}">
    <h2></h2>

    <h2 class="sub">
        {{ $data->cabang->nama_cabang ?? 'Outdoorkriss Tegalsari' }}
</h2>

    <div class="sub">
        @if($data->cabang)
            {{ $data->cabang->lokasi ?? '-' }} |
            {{ $data->penyewa->user->no_telepon ?? '-' }}
        @else
            {{ optional($data->adminPusat)->user->alamat ?? '-' }} |
            {{ $data->penyewa->user->no_telepon ?? '-' }}
        @endif
    </div>

    <div class="sub">
        {{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('l, d M Y') }}
    </div>
</div>

<!-- INFO -->
<div class="info">

    <div class="row">
        <span class="label">Pelanggan</span>
        <span class="value">{{ $data->penyewa->user->nama ?? '-' }}</span>
    </div>

    <div class="row">
        <span class="label">Admin</span>
        <span class="value">
            {{
                $data->cabang
                ? optional($data->cabang->adminCabang->first())->user->nama ?? '-'
                : optional($data->adminPusat)->user->nama ?? '-'
            }}
        </span>
    </div>

    <div class="row">
        <span class="label">Metode</span>
        <span class="value">{{ strtoupper($data->metode_bayar) }}</span>
    </div>

    <div class="row">
        <span class="label">Sewa</span>
        <span class="value">
            {{ \Carbon\Carbon::parse($data->tanggal_sewa)->translatedFormat('l, d M Y') }}
        </span>
    </div>

    <div class="row">
        <span class="label">Selesai</span>
        <span class="value">
            {{ \Carbon\Carbon::parse($data->tanggal_selesai)->translatedFormat('l, d M Y') }}
        </span>
    </div>

</div>

<!-- ITEM -->
<div style="margin-top:10px;">

@php
    $durasi = (int) \Carbon\Carbon::parse($data->tanggal_sewa)
        ->diffInDays($data->tanggal_selesai);
@endphp

@foreach($data->itemPenyewaan as $item)

<div class="item">

    <div class="item-name">
        {{ $item->type == 'paket'
            ? $item->paket->nama_paket
            : ($item->produk->nama_produk ?? '-') }}
    </div>

    <div class="item-sub">
        Rp {{ number_format($item->harga,0,',','.') }} / hari
    </div>

    <div class="item-subtotal">
        <span>{{ $item->qty }} × {{ $durasi }} hari</span>
        <span>Rp {{ number_format($item->subtotal,0,',','.') }}</span>
    </div>

</div>

@endforeach

</div>

<!-- TOTAL -->
<div class="total">
    <span>TOTAL</span>
    <span>Rp {{ number_format($data->total,0,',','.') }}</span>
</div>

<!-- FOOTER -->
<div class="footer">
    <strong>Terima kasih telah mempercayai OUTDOORKRISS</strong>
    Semoga perjalanan Anda menyenangkan 🌿<br>
    Sampai jumpa di petualangan berikutnya
</div>

</div>

<!-- BUTTON -->
<div class="btn">
    <button onclick="downloadStruk()">Unduh Struk</button>
</div>

</div>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<script>
function downloadStruk() {
    const element = document.querySelector(".receipt");

    html2canvas(element, {
        scale: 2,
        useCORS: true
    }).then(canvas => {
        const link = document.createElement("a");
        link.download = "struk-penyewaan.png";
        link.href = canvas.toDataURL("image/png");
        link.click();
    });
}
</script>

</body>
</html>