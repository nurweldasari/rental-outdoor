@extends('layouts.app')

@section('title','Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/detail_penyewaan.css') }}">
@endpush 

@section('content')
<div class="detail-wrapper">

    {{-- HEADER --}}
    @php
        $statusClass = match($penyewaan->status_penyewaan) {
            'menunggu_pembayaran' => 'pending',
            'sedang_disewa'       => 'active',
            'selesai'             => 'done',
            'dibatalkan'          => 'cancel',
            default               => ''
        };
    @endphp

    <div class="detail-title-bar">
        <span class="title-text">Detail Penyewaan</span>

        <span class="badge-status {{ $statusClass }}">
            {{ ucfirst(str_replace('_',' ', $penyewaan->status_penyewaan)) }}
        </span>
    </div>


    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif


    {{-- INFORMASI PENYEWA --}}
    <div class="info-card">
        <div class="info-table">

            <div class="info-cell label">Penyewa</div>
            <div class="info-cell value">
                {{ optional($penyewaan->penyewa->user)->nama }}
            </div>

            <div class="info-cell label">Tanggal sewa</div>
            <div class="info-cell value">
                {{ \Carbon\Carbon::parse($penyewaan->tanggal_sewa)->format('d M Y') }}
            </div>

            <div class="info-cell label">No. Telephone</div>
            <div class="info-cell value">
                {{ optional($penyewaan->penyewa->user)->no_telepon }}
            </div>

            <div class="info-cell label">Status</div>
            <div class="info-cell value">
                {{ ucfirst(str_replace('_',' ', $penyewaan->status_penyewaan)) }}
            </div>

            <div class="info-cell label">Metode Pembayaran</div>
            <div class="info-cell value">
                {{ ucfirst($penyewaan->metode_bayar) }}
            </div>

            <div class="info-cell label">Bukti Pembayaran</div>
            <div class="info-cell value">
                @if($penyewaan->bukti_bayar)
                    <a href="{{ asset($penyewaan->bukti_bayar) }}" 
                       target="_blank" 
                       class="btn-bukti">
                        Lihat Bukti Pembayaran
                    </a>
                @else
                    -
                @endif
            </div>

        </div>
    </div>


    {{-- TABEL PRODUK --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Produk</th>
                    <th>Jumlah Produk</th>
                    <th>Tanggal Berakhir</th>
                    <th>Tanggal Kembali</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @php $no = 1; @endphp

                @foreach($penyewaan->itemPenyewaan as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <td>{{ $item->qty }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($penyewaan->tanggal_selesai)->format('d M Y') }}
                    </td>

                    <td>
                        @if($penyewaan->tanggal_kembali)
                            {{ \Carbon\Carbon::parse($penyewaan->tanggal_kembali)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </td>

                    <td>Rp {{ number_format($item->harga,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="6" class="total-label">Total</td>
                    <td class="total-value">
                        Rp {{ number_format($penyewaan->total,0,',','.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    {{-- ACTION BUTTON --}}
    <div class="action-buttons">

        {{-- KONFIRMASI BAYAR --}}
        @if($penyewaan->status_penyewaan == 'menunggu_pembayaran')
        <form action="{{ route('admin.konfirmasi_bayar', $penyewaan->idpenyewaan) }}" method="POST">
            @csrf
            <button class="btn-konfirmasi">
                Konfirmasi Pembayaran
            </button>
        </form>
        @endif


        {{-- SELESAIKAN --}}
        @if($penyewaan->status_penyewaan == 'sedang_disewa')
        <form action="{{ route('admin.penyewaan.selesai', $penyewaan->idpenyewaan) }}" method="POST">
            @csrf
            <button class="btn-konfirmasi">
                Selesaikan Penyewaan
            </button>
        </form>
        @endif


        <button type="button" 
                class="btn-kembali"
                onclick="window.history.back()">
            Kembali
        </button>

    </div>

</div>
@endsection
