<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StokCabang;
use App\Models\Produk;
use App\Models\Paket;

class CartController extends Controller
{
    /* ================= PRODUK ADD ================= */
    public function add(Request $request)
    {
        $request->validate([
            'idstok' => ['required', 'integer', 'exists:stok_cabang,idstok'],
        ]);

        $cabangId = session('cabang_id');

        if (!$cabangId) {
            return response()->json(['error' => 'Cabang belum dipilih'], 403);
        }

        $stok = StokCabang::with('produk')
            ->where('idstok', $request->idstok)
            ->where('cabang_idcabang', $cabangId)
            ->first();

        if (!$stok) {
            return response()->json(['error' => 'Stok tidak ditemukan di cabang ini'], 403);
        }

        $cart = session()->get('cart', []);
        $currentQty = $cart[$stok->idstok]['qty'] ?? 0;

        if ($currentQty >= $stok->jumlah) {
            return response()->json(['error' => 'Stok tidak mencukupi'], 422);
        }

        $cart[$stok->idstok] = [
            'type'   => 'produk',
            'idstok' => $stok->idstok,
            'nama'   => $stok->produk->nama_produk,
            'harga'  => $stok->produk->harga,
            'qty'    => $currentQty + 1
        ];

        session(['cart' => $cart]);

        return response()->json($cart);
    }


    /* ================= PRODUK UPDATE ================= */
    public function update(Request $request)
    {
        $request->validate([
            'idstok' => ['required', 'integer'],
            'qty'    => ['required', 'integer', 'min:0', 'max:1000'],
        ]);

        $cabangId = session('cabang_id');

        if (!$cabangId) {
            return response()->json(['error' => 'Cabang belum dipilih'], 403);
        }

        $cart = session('cart', []);

        if (!isset($cart[$request->idstok])) {
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        }

        // kalau qty 0 hapus
        if ($request->qty <= 0) {
            unset($cart[$request->idstok]);
            session(['cart' => $cart]);
            return response()->json($cart);
        }

        // cek stok
        $stok = StokCabang::where('idstok', $request->idstok)
            ->where('cabang_idcabang', $cabangId)
            ->first();

        if (!$stok) {
            return response()->json(['error' => 'Stok tidak valid'], 403);
        }

        if ($request->qty > $stok->jumlah) {
            return response()->json(['error' => 'Qty melebihi stok tersedia'], 422);
        }

        $cart[$request->idstok]['qty'] = $request->qty;

        session(['cart' => $cart]);

        return response()->json($cart);
    }


    /* ================= PRODUK DELETE ================= */
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


    /* ================= PAKET ADD ================= */
    public function addPaket(Request $request)
    {
        $paket = Paket::with('detail.stokCabang.produk')
            ->findOrFail($request->paket_id);

        $cart = session('cart', []);

        $items = [];

        foreach ($paket->detail as $d) {
            $items[] = [
                'type'   => 'produk_dalam_paket',
                'idstok' => $d->stokCabang->idstok,
                'nama'   => $d->stokCabang->produk->nama_produk,
                'qty'    => $d->qty,
                'harga'  => $d->stokCabang->produk->harga,
            ];
        }

        $cart['paket_'.$paket->id] = [
            'type'      => 'paket',
            'paket_id'  => $paket->id,
            'nama'      => $paket->nama_paket,
            'harga'     => $paket->harga_paket,
            'qty'       => 1,
            'items'     => $items
        ];

        session(['cart' => $cart]);

        return response()->json($cart);
    }


    /* ================= PAKET DELETE ================= */
    public function deletePaket(Request $request)
    {
        $request->validate([
            'paket_id' => ['required', 'integer'],
        ]);

        $cart = session('cart', []);

        unset($cart['paket_'.$request->paket_id]);

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
