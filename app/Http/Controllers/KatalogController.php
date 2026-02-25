<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\Kategori;
use App\Models\StokCabang;
use App\Models\Produk;
use App\Models\User;
use App\Models\Rekening;

class KatalogController extends Controller
{
  public function pilihCabang($id)
{
    $cabang = Cabang::where('idcabang', $id)->firstOrFail();

    // ⛔ CEK STATUS CABANG (HANYA BOLEH AKTIF)
    if ($cabang->status_cabang !== 'aktif') {
        abort(403, 'Cabang belum aktif atau belum dikonfirmasi');
    }

    session([
        'tipe_toko' => 'cabang',      
        'toko_id'   => $cabang->idcabang,

        'cabang_id'   => $cabang->idcabang,
        'cabang_nama' => $cabang->nama_cabang
    ]);

    // 🔥 RESET KERANJANG SAAT GANTI CABANG
    session()->forget('cart');


    // ⬇️ SETELAH PILIH CABANG MASUK KATALOG
    return redirect()->route('katalog_produk');
}

public function katalogCabang()
{
    // proteksi
    if (!session()->has('cabang_id')) {
        return redirect()->route('dashboard');
    }

    $cabangId = session('cabang_id');

    $produkList = StokCabang::where('cabang_idcabang', $cabangId)
                     ->where('is_active', 1)
                     ->where('jumlah', '>', 0)
                     ->get();

    // ambil kategori untuk filter
    $kategoriList = Kategori::all();

    $rekening = Rekening::where('cabang_idcabang', $cabangId)->first();
    return view('katalog_produk', compact('produkList','kategoriList','rekening'));
}  
public function pilihPusat($id)
{
    $pusat = User::where('idusers', $id)->firstOrFail();

    // simpan ke session
    session([
         'tipe_toko' => 'pusat',      
         'toko_id'   => $pusat->idusers, 

        'pusat_id'   => $pusat->idusers,
        'pusat_nama' => $pusat->name // sesuaikan dengan kolom di tabel users
    ]);

    // reset keranjang kalau ada
    session()->forget('cart');

    return redirect()->route('katalog_pusat');
}
public function katalogPusat()
{
    if (!session()->has('pusat_id')) {
        return redirect()->route('dashboard');
    }

    $pusatId = session('pusat_id');

    $produkList = \App\Models\Produk::where(
        'admin_pusat_idadmin_pusat',
        $pusatId
    )->get();

    $kategoriList = Kategori::all();

    return view('katalog_pusat', compact('produkList','kategoriList'));
}
}