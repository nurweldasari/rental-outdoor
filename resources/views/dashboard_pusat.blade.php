@extends('layouts.app')

@php
    $active = 'dashboard';
@endphp

@section('title','Dashboard')

@section('content')

<div class="filter">
  <select>
    <option>Pilih Tahun</option>
    <option>2024</option>
    <option>2025</option>
  </select>
</div>

<section class="cards">

  <div class="card">
    <i class="fa-solid fa-user"></i>
    <h2>250</h2>
    <p>Total Penyewa</p>
  </div>

  <div class="card">
    <i class="fa-solid fa-cart-shopping"></i>
    <h2>50</h2>
    <p>Total Penyewaan</p>
  </div>

  <div class="card">
    <i class="fa-solid fa-store"></i>
    <h2>150</h2>
    <p>Total Alat</p>
  </div>

  <div class="card">
    <i class="fa-solid fa-layer-group"></i>
    <h2>15</h2>
    <p>Kategori</p>
  </div>

</section>

@endsection
