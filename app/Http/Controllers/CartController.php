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
    $stok = StokCabang::with('produk')->findOrFail($request->idstok);

    $cart = session()->get('cart', []);

    $currentQty = $cart[$stok->idstok]['qty'] ?? 0;

    // 🚫 CEK STOK
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
    $stok = StokCabang::findOrFail($request->idstok);
    $cart = session('cart', []);

    // hapus jika qty 0
    if ($request->qty <= 0) {
        unset($cart[$request->idstok]);
        session(['cart' => $cart]);
        return response()->json($cart);
    }

    // 🚫 CEK STOK
    if ($request->qty > $stok->jumlah) {
        return response()->json([
            'error' => 'Qty melebihi stok'
        ], 422);
    }

    $cart[$request->idstok]['qty'] = $request->qty;
    session(['cart' => $cart]);

    return response()->json($cart);
}

    public function delete(Request $request)
    {
        $cart = session('cart');
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
