@extends('layouts.app')
@php $active='bagi_hasil'; @endphp
@section('title','Bagi Hasil Cabang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/bagi_hasil.css') }}">
@endpush

@section('content')
<div class="bagi-container">

@if($view=='detail')

@php
$item=$bagiHasilAktif??null;
$totalPendapatan=$item?($item->nominal_owner+$item->nominal_cabang):0;
@endphp

<div class="detail-header">
<div class="action-row">
<div class="action-left">
<button class="btn btn-dark" onclick="openModal()">Upload Bukti Fee</button>
<a href="{{ route('bagi_hasil.cabang') }}?view=riwayat" class="btn btn-riwayat">Riwayat Bagi Hasil</a>
</div>
</div>
</div>

<div class="detail-wrapper">
<div class="detail-card-custom">

<div class="rekening-pill">
Rekening Tujuan Fee:<br>
Mandiri - 98767896540 <br>
a.n OwnerOutdoorKriss
</div>

<h5 class="detail-title-center">Detail Perhitungan Bagi Hasil</h5>

@if($item)

<div class="total-pill">
<span>Total Pendapatan Cabang</span>
<strong>Rp {{ number_format($totalPendapatan,0,',','.') }}</strong>
</div>

<div class="hasil-box">
<div class="hasil-header">
<strong>Owner</strong>
<span>{{ $item->presentase_owner }}%</span>
</div>

<div class="row-between">
<span>Perhitungan:</span>
<span>Rp {{ number_format($totalPendapatan,0,',','.') }} × {{ $item->presentase_owner }}%</span>
</div>

<hr>

<div class="row-between">
<strong>Hasil :</strong>
<strong>Rp {{ number_format($item->nominal_owner,0,',','.') }}</strong>
</div>
</div>

<div class="hasil-box">
<div class="hasil-header">
<strong>Admin Cabang</strong>
<span>{{ $item->presentase_cabang }}%</span>
</div>

<div class="row-between">
<span>Perhitungan:</span>
<span>Rp {{ number_format($totalPendapatan,0,',','.') }} × {{ $item->presentase_cabang }}%</span>
</div>

<hr>

<div class="row-between">
<strong>Hasil :</strong>
<strong>Rp {{ number_format($item->nominal_cabang,0,',','.') }}</strong>
</div>
</div>

@else
<div style="text-align:center;padding:40px;">
Belum ada perhitungan bagi hasil dari owner
</div>
@endif

</div>
</div>


<div id="modalUpload" class="modal">
<div class="modal-content">

<div class="modal-header">
<h4>Upload Bukti Fee</h4>
<span class="modal-close" onclick="closeModal()">
<i class="fa-solid fa-xmark"></i>
</span>
</div>

@if($item)

<form action="{{ route('bagi_hasil.upload',$item->idbagi_hasil) }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="upload-box">
<label for="bukti_fee" class="upload-label">
<i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
<p id="uploadText">Upload bukti transfer sesuai nominal bagi hasil owner</p>
</label>

<input type="file" name="bukti_fee" id="bukti_fee" required onchange="showFileName()">
</div>

<button type="submit" class="btn-simpan">Simpan</button>
</form>

@endif

@if($item && $item->bukti_fee)

<div class="tanggal-upload">
<div class="tanggal-info">
<span class="tanggal-title">Tanggal Upload</span>
<span class="tanggal-date">{{ \Carbon\Carbon::parse($item->updated_at)->format('d F Y') }}</span>
</div>

<span class="badge 
{{ $item->status=='menunggu'?'badge-menunggu':'' }}
{{ $item->status=='terkonfirmasi'?'badge-berhasil':'' }}
{{ $item->status=='ditolak'?'badge-ditolak':'' }}">
{{ ucfirst($item->status) }}
</span>

</div>
@endif

</div>
</div>

@endif



@if($view=='riwayat')

<a href="{{ route('bagi_hasil.cabang') }}" class="btn-back-red">
<i class="fa-solid fa-arrow-left"></i> Kembali
</a>

<div class="riwayat-container">

<h3 class="riwayat-title">Riwayat Bagi Hasil Cabang</h3>

<table class="table-riwayat">

<thead>
<tr>
<th>Tanggal Upload</th>
<th>Total Pendapatan</th>
<th>Bagi Hasil Owner</th>
<th>Bagi Hasil Admin Cabang</th>
<th>Bukti Transfer</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@forelse($riwayat as $item)
<tr>

<td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d F Y') }}</td>

<td>
Rp {{ number_format($item->nominal_owner+$item->nominal_cabang,0,',','.') }}
</td>

<td>
Rp {{ number_format($item->nominal_owner,0,',','.') }}
</td>

<td>
Rp {{ number_format($item->nominal_cabang,0,',','.') }}
</td>

<td>
@if($item->bukti_fee)
<a href="{{ asset('storage/'.$item->bukti_fee) }}" target="_blank" class="btn-eye">
<i class="fa-solid fa-eye"></i>
</a>
@else
-
@endif
</td>

<td class="status-text">
{{ ucfirst($item->status) }}
</td>

</tr>

@empty
<tr>
<td colspan="6" style="text-align:center;">
Belum ada riwayat bagi hasil
</td>
</tr>
@endforelse

</tbody>
</table>

</div>

@endif

</div>


@if(session('openModal'))
<script>
window.onload=function(){
document.getElementById('modalUpload').style.display='flex';
}
</script>
@endif


<script>
function openModal(){document.getElementById('modalUpload').style.display='flex';}
function closeModal(){document.getElementById('modalUpload').style.display='none';}
function showFileName(){
let input=document.getElementById('bukti_fee');
let text=document.getElementById('uploadText');
if(input.files.length>0){text.innerHTML=input.files[0].name;}
}
</script>

@endsection