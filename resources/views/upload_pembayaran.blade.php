@extends('layouts.app')

@section('title','Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/upload_pembayaran.css') }}">
@endpush 

@section('content')

    <!-- INFO PEMBAYARAN -->

    <!-- UPLOAD AREA -->
    <form action="{{ route('penyewaan.upload_bukti', $penyewaan->idpenyewaan) }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    <div class="upload-wrapper">
        <h2 class="upload-title">Upload Bukti Bayar</h2>

        <div class="info-bayar">
            <div class="info-left">
                <p>Metode Pembayaran : <strong>{{ ucfirst($penyewaan->metode_bayar) }}</strong></p>
                <p class="total">
                    Total : <span>Rp {{ number_format($penyewaan->total) }}</span>
                </p>
            </div>

            <div class="info-right">
                @if($rekening)
                    <span class="badge bank">
                        {{ $rekening->nama_bank }}
                    </span>

                    <span class="badge rekening">
                        No. rekening : {{ $rekening->no_rekening }}
                        ({{ $rekening->atas_nama }})
                    </span>
                @endif
            </div>
        </div>

        <label class="upload-box">
            <input type="file" name="bukti_bayar" hidden>
            <div class="upload-content">
                <i class="icon-upload"><i class="fa-solid fa-cloud"></i></i>
                <p>upload bukti bayar</p>
            </div>
        </label>

        <div class="action-button">
            <button type="submit" class="btn-konfirmasi">Konfirmasi</button>
            <a href="{{ route('penyewaan.riwayat') }}" class="btn-batal">Batal</a>
        </div>
    </div>
</form>


</div>
@endsection