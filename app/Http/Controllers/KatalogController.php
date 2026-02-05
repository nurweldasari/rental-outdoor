<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\Kategori;
use App\Models\StokCabang;

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
        return redirect()->route('pilih.cabang.page');
    }

    $cabangId = session('cabang_id');

    $produkList = StokCabang::where('cabang_idcabang', $cabangId)
                     ->where('is_active', 1)
                     ->where('jumlah', '>', 0)
                     ->get();

    // ambil kategori untuk filter
    $kategoriList = Kategori::all();

    return view('katalog_produk', compact('produkList','kategoriList'));
}  
}
