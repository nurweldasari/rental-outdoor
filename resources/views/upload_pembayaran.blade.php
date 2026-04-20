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
        <span class="timer" data-time="{{ $sisaDetik }}"><i class="fa-solid fa-clock"></i>
        <strong class="countdown">-- : -- : --</strong></span>

        <div class="info-bayar">
            <div class="info-left">
                <p>Metode Pembayaran : <strong>{{ ucfirst($penyewaan->metode_bayar) }}</strong></p>
                <p class="total">
                    Total : <span>Rp. {{ number_format($penyewaan->total,  0, ',', '.') }}</span>
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
            <input type="file" name="bukti_bayar" id="uploadInput" hidden>

            <div class="upload-content">
                <i class="icon-upload"><i class="fa-solid fa-cloud-arrow-up"></i></i>
                <p id="fileName">Upload bukti bayar</p>
            </div>
        </label>

        <div class="action-button">
            <button type="submit" class="btn-konfirmasi">Konfirmasi</button>
            <a href="{{ route('item_penyewaan') }}" class="btn-batal">Batal</a>
        </div>
    </div>
</form>


</div>
<script>
const input = document.getElementById('uploadInput');
const fileText = document.getElementById('fileName');

input.addEventListener('change', function () {
    const fileName = this.files[0]?.name;

    if (fileName) {
        fileText.textContent = fileName;
        fileText.classList.add('active');
    }
});
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
@endsection