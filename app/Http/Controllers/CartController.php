<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StokCabang;
use App\Models\Produk;

class CartController extends Controller
{
    public function add(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer', 'exists:stok_cabang,idstok'],
    ]);

    $cabangId = session('cabang_id'); // cabang aktif

    if (!$cabangId) {
        return response()->json(['error' => 'Cabang belum dipilih'], 403);
    }

    // 🔒 VALIDASI stok harus milik cabang aktif
    $stok = StokCabang::with('produk')
        ->where('idstok', $request->idstok)
        ->where('cabang_idcabang', $cabangId)
        ->first();

    if (!$stok) {
        return response()->json([
            'error' => 'Stok tidak ditemukan di cabang ini'
        ], 403);
    }

    $cart = session()->get('cart', []);
    $currentQty = $cart[$stok->idstok]['qty'] ?? 0;

    if ($currentQty >= $stok->jumlah) {
        return response()->json([
            'error' => 'Stok tidak mencukupi'
        ], 422);
    }

    $cart[$stok->idstok] = [
        'idstok' => $stok->idstok,
        'nama'   => $stok->produk->nama_produk,
        'harga'  => $stok->produk->harga,
        'qty'    => $currentQty + 1
    ];

    session(['cart' => $cart]);

    return response()->json($cart);
}


    public function update(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer', 'exists:stok_cabang,idstok'],
        'qty'    => ['required', 'integer', 'min:0', 'max:1000'],
    ]);

    $cabangId = session('cabang_id');

    if (!$cabangId) {
        return response()->json(['error' => 'Cabang belum dipilih'], 403);
    }

    $stok = StokCabang::where('idstok', $request->idstok)
        ->where('cabang_idcabang', $cabangId)
        ->first();

    if (!$stok) {
        return response()->json([
            'error' => 'Stok tidak valid untuk cabang ini'
        ], 403);
    }

    $cart = session('cart', []);

    if ($request->qty <= 0) {
        unset($cart[$request->idstok]);
        session(['cart' => $cart]);
        return response()->json($cart);
    }

    if ($request->qty > $stok->jumlah) {
        return response()->json([
            'error' => 'Qty melebihi stok tersedia'
        ], 422);
    }

    $cart[$request->idstok]['qty'] = $request->qty;
    session(['cart' => $cart]);

    return response()->json($cart);
}

    public function delete(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer'],
    ]);

    $cart = session('cart', []);

    unset($cart[$request->idstok]);

    session(['cart' => $cart]);

    return response()->json($cart);
}

   public function addPusat(Request $request)
{
    $produk = Produk::findOrFail($request->idproduk);

    $cart = session()->get('cart', []);

    $currentQty = $cart[$produk->idproduk]['qty'] ?? 0;

    // cek stok pusat
    if ($currentQty >= $produk->stok_pusat) {
        return response()->json([
            'error' => 'Stok tidak mencukupi'
        ], 422);
    }

    $cart[$produk->idproduk] = [
        'idproduk' => $produk->idproduk,
        'nama'     => $produk->nama_produk,
        'harga'    => $produk->harga,
        'qty'      => $currentQty + 1
    ];

    session(['cart' => $cart]);

    return response()->json($cart);
}

    public function updatePusat(Request $request)
{
    $produk = Produk::findOrFail($request->idproduk);
    $cart = session('cart', []);

    if ($request->qty <= 0) {
        unset($cart[$request->idproduk]);
        session(['cart' => $cart]);
        return response()->json($cart);
    }

    if ($request->qty > $produk->stok_pusat) {
        return response()->json([
            'error' => 'Qty melebihi stok'
        ], 422);
    }

    $cart[$request->idproduk]['qty'] = $request->qty;
    session(['cart' => $cart]);

    return response()->json($cart);
}

    public function deletePusat(Request $request)
    {
        $cart = session('cart');
        unset($cart[$request->idproduk]);

        session(['cart' => $cart]);

        return response()->json($cart);
    }
}
