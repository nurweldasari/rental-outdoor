<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\Kategori;
use App\Models\StokCabang;
use App\Models\Produk;
use App\Models\User;
use App\Models\Paket;
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

public function katalogCabang(Request $request)
{
    // proteksi
    if (!session()->has('cabang_id')) {
        return redirect()->route('dashboard');
    }

    $cabangId = session('cabang_id');

    $produkList = StokCabang::with('produk') // biar bisa search nama produk
        ->where('cabang_idcabang', $cabangId)
        ->where('is_active', 1)
        ->where('jumlah', '>', 0)

        // 🔍 SEARCH
        ->when($request->search, function ($q) use ($request) {
            $q->whereHas('produk', function ($p) use ($request) {
                $p->where('nama_produk', 'like', '%' . $request->search . '%');
            });
        })

        // 📂 FILTER KATEGORI
        ->when($request->kategori, function ($q) use ($request) {
            $q->whereHas('produk', function ($p) use ($request) {
                $p->where('kategori_idkategori', $request->kategori);
            });
        })

        ->paginate(10);

    // kategori untuk filter
    $kategoriList = Kategori::all();

    $rekening = Rekening::where('cabang_idcabang', $cabangId)->first();

    $paketList = Paket::with('detail.stokCabang.produk')
        ->where('cabang_id', $cabangId)
        ->whereDoesntHave('detail', function ($q) {
            $q->whereHas('stokCabang', function ($s) {
                $s->whereColumn('stok_cabang.jumlah', '<', 'paket_detail.qty')
                  ->orWhere('stok_cabang.is_active', 0);
            });
        })
        ->get();

    return view('katalog_produk', compact(
        'produkList',
        'kategoriList',
        'rekening',
        'paketList'
    ));
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
public function katalogPusat(Request $request)
{
    if (!session()->has('pusat_id')) {
        return redirect()->route('dashboard');
    }

    $pusatId = session('pusat_id');

    $produkList = Produk::where('admin_pusat_idadmin_pusat', $pusatId)

        // 🔍 SEARCH
        ->when($request->search, function ($q) use ($request) {
            $q->where('nama_produk', 'like', '%' . $request->search . '%');
        })

        // 📂 FILTER KATEGORI
        ->when($request->kategori, function ($q) use ($request) {
            $q->where('kategori_idkategori', $request->kategori);
        })

        // 🔥 FILTER SKALA (INI YANG KAMU BUTUH)
        ->when($request->skala, function ($q) use ($request) {
            $q->where('jenis_skala', $request->skala);
        })

        
        ->paginate(10); // pakai paginate biar rapi

    $kategoriList = Kategori::all();
    $paketList = Paket::with('detail.produk')
    ->whereNull('cabang_id')
    ->get();

    return view('katalog_pusat', compact('produkList','kategoriList', 'paketList'));
}
}