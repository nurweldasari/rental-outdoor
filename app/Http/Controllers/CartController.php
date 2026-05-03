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
        return response()->json([
            'error' => 'Cabang belum dipilih',
            'cart'  => session('cart', [])
        ], 403);
    }

    $stok = StokCabang::with('produk')
        ->where('idstok', $request->idstok)
        ->where('cabang_idcabang', $cabangId)
        ->first();

    if (!$stok) {
        return response()->json([
            'error' => 'Stok tidak ditemukan',
            'cart'  => session('cart', [])
        ], 403);
    }

    $cart = session('cart', []);

// HITUNG TOTAL TERPAKAI
$usedStock = 0;

foreach ($cart as $key => $item) {

    // skip item yang sedang diupdate
    if ($key == $request->idstok) continue;

    // produk biasa
    if (($item['type'] ?? '') === 'produk' && $item['idstok'] == $request->idstok) {
        $usedStock += $item['qty'];
    }

    // paket
    if (($item['type'] ?? '') === 'paket') {
        foreach ($item['items'] as $i) {
            if ($i['idstok'] == $request->idstok) {
                $usedStock += $i['qty'] * $item['qty'];
            }
        }
    }
}
    $currentQty = $cart[$stok->idstok]['qty'] ?? 0;

// total setelah ditambah
$totalDipakai = $usedStock + $currentQty + 1;

if ($totalDipakai > $stok->jumlah) {
    return response()->json([
        'error' => 'Stok tidak mencukupi',
        'cart'  => $cart
    ], 422);
}

    $cart[$stok->idstok] = [
        'type'   => 'produk',
        'idstok' => $stok->idstok,
        'nama'   => $stok->produk->nama_produk,
        'harga'  => $stok->produk->harga,
        'qty'    => $currentQty + 1,
        'max'    => $stok->jumlah - $usedStock 
    ];

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
public function update(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer'],
        'qty'    => ['required', 'integer', 'min:0', 'max:1000'],
    ]);

    $cabangId = session('cabang_id');

    if (!$cabangId) {
        return response()->json([
            'error' => 'Cabang belum dipilih',
            'cart'  => session('cart', [])
        ], 403);
    }

    $cart = session('cart', []);

    if (!isset($cart[$request->idstok])) {
        return response()->json([
            'error' => 'Item tidak ditemukan',
            'cart'  => $cart
        ], 404);
    }

    $stok = StokCabang::where('idstok', $request->idstok)
        ->where('cabang_idcabang', $cabangId)
        ->first();

    if (!$stok) {
        return response()->json([
            'error' => 'Stok tidak valid',
            'cart'  => $cart
        ], 403);
    }

    // HITUNG USED STOCK (WAJIB ADA)
    $usedStock = 0;

    foreach ($cart as $key => $item) {

        if ($key == $request->idstok) continue;

        if (($item['type'] ?? '') === 'produk' && $item['idstok'] == $request->idstok) {
            $usedStock += $item['qty'];
        }

        if (($item['type'] ?? '') === 'paket') {
            foreach ($item['items'] as $i) {
                if ($i['idstok'] == $request->idstok) {
                    $usedStock += $i['qty'] * $item['qty'];
                }
            }
        }
    }

    // HITUNG AVAILABLE
    $available = $stok->jumlah - $usedStock;

    if ($request->qty <= 0) {
        unset($cart[$request->idstok]);
        session(['cart' => $cart]);
        return response()->json(['cart' => $cart]);
    }

    // CEK STOK TERPAKAI PAKET
    if ($request->qty > $available) {
        $cart[$request->idstok]['qty'] = $available;

        session(['cart' => $cart]);

        return response()->json([
            'error' => 'Stok tidak cukup (dipakai paket)',
            'max'   => $available,
            'cart'  => $cart
        ]);
    }

    // CEK STOK ASLI
    if ($request->qty > $stok->jumlah) {
        $cart[$request->idstok]['qty'] = $stok->jumlah;

        session(['cart' => $cart]);

        return response()->json([
            'error' => 'Qty melebihi stok',
            'max'   => $stok->jumlah,
            'cart'  => $cart
        ]);
    }

    // NORMAL
    $cart[$request->idstok]['qty'] = $request->qty;

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
public function delete(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer'],
    ]);

    $cart = session('cart', []);

    unset($cart[$request->idstok]);

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}

    /* ================= PAKET ADD ================= */
  public function addPaket(Request $request)
{
    $paket = Paket::with('detail.stokCabang.produk')
        ->findOrFail($request->paket_id);

    $cart = session('cart', []);

    $key = 'paket_'.$paket->id;   // 🔥 INI WAJIB ADA
    $maxPaket = PHP_INT_MAX;      // 🔥 INI WAJIB ADA

    $items = [];

    foreach ($paket->detail as $d) {
        $stok = $d->stokCabang;

        if (!$stok) continue;

        $items[] = [
            'type'   => 'produk_dalam_paket',
            'idstok' => $stok->idstok,
            'nama'   => $stok->produk->nama_produk,
            'qty'    => $d->qty,
            'harga'  => $stok->produk->harga,
        ];

        // hitung stok cabang
        $usedStock = 0;

        foreach ($cart as $item) {
            if (($item['type'] ?? '') === 'produk' && ($item['idstok'] ?? null) == $stok->idstok) {
                $usedStock += $item['qty'];
            }

            if (($item['type'] ?? '') === 'paket') {
                foreach ($item['items'] as $i) {
                    if ($i['idstok'] == $stok->idstok) {
                        $usedStock += $i['qty'] * $item['qty'];
                    }
                }
            }
        }

        $available = $stok->jumlah - $usedStock;
        $maxItem = floor($available / $d->qty);

        $maxPaket = min($maxPaket, $maxItem);
    }

    if ($maxPaket <= 0) {
        return response()->json([
            'error' => 'Stok paket tidak cukup',
            'cart' => $cart
        ], 422);
    }

    if (isset($cart[$key])) {
        $newQty = $cart[$key]['qty'] + 1;

        if ($newQty > $maxPaket) {
            return response()->json([
                'error' => 'Melebihi stok paket',
                'max' => $maxPaket,
                'cart' => $cart
            ], 422);
        }

        $cart[$key]['qty'] = $newQty;
        $cart[$key]['max'] = $maxPaket;

    } else {
        $cart[$key] = [
            'type'     => 'paket',
            'paket_id' => $paket->id,
            'nama'     => $paket->nama_paket,
            'harga'    => $paket->harga_paket,
            'qty'      => 1,
            'max'      => $maxPaket,
            'items'    => $items
        ];
    }

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
public function updatePaket(Request $request)
{


    $request->validate([
        'paket_id' => ['required', 'integer'],
        'qty'      => ['required', 'integer', 'min:0', 'max:100'],
    ]);

    $cart = session('cart', []);
    $key = $request->key ?? 'paket_'.$request->paket_id;

    if (!isset($cart[$key])) {
        return response()->json([
            'error' => 'Paket tidak ditemukan',
            'cart'  => $cart
        ], 404);
    }

    $paket = Paket::with('detail.stokCabang')->find($request->paket_id);

    if (!$paket) {
        return response()->json([
            'error' => 'Data paket tidak valid',
            'cart'  => $cart
        ], 403);
    }

    // hapus kalau qty 0
    if ($request->qty <= 0) {
        unset($cart[$key]);
        session(['cart' => $cart]);

        return response()->json([
            'cart' => $cart
        ]);
    }

    // HITUNG MAX PAKET GLOBAL
   $maxPaket = PHP_INT_MAX;

foreach ($paket->detail as $d) {

    $stok = $d->stokCabang;
    if (!$stok || $d->qty <= 0) continue;

    $usedStock = 0;

    foreach ($cart as $key => $item) {

        // skip paket yg sedang diupdate
        if ($key == 'paket_'.$request->paket_id) continue;

        // PRODUK LANGSUNG
        if (($item['type'] ?? '') === 'produk' && $item['idstok'] == $stok->idstok) {
            $usedStock += $item['qty'];
        }

        // PAKET LAIN
        if (($item['type'] ?? '') === 'paket') {
            foreach ($item['items'] as $i) {
                if ($i['idstok'] == $stok->idstok) {
                    $usedStock += $i['qty'] * $item['qty'];
                }
            }
        }
    }

    // stok sisa
    $available = $stok->jumlah - $usedStock;

    // max paket dari item ini
    $maxItem = floor($available / $d->qty);

    $maxPaket = min($maxPaket, $maxItem);
}

    // kalau melebihi stok
    if ($request->qty > $maxPaket) {

        $cart[$key]['qty'] = $maxPaket;
        $cart[$key]['max'] = $maxPaket;

        session(['cart' => $cart]);

        return response()->json([
            'error' => 'Melebihi stok paket',
            'max'   => $maxPaket,
            'cart'  => $cart
        ]);
    }

    // update normal
    $cart[$key]['qty'] = $request->qty;
    $cart[$key]['max'] = $maxPaket;

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
public function deletePaket(Request $request)
{
    $request->validate([
        'paket_id' => ['required', 'integer'],
    ]);

    $cart = session('cart', []);

    unset($cart['paket_'.$request->paket_id]);

    session(['cart' => $cart]);

    // FIX DI SINI
    return response()->json([
        'cart' => $cart
    ]);
}

/* ================= PRODUK Pusat ================= */
  public function addPusat(Request $request)
{
    $request->validate([
        'idproduk' => 'required|exists:produk,idproduk'
    ]);

    $produk = Produk::findOrFail($request->idproduk);

    $cart = session('cart', []);
    $key = 'produk_'.$produk->idproduk;

    $usedStock = 0;

    foreach ($cart as $k => $item) {

        // skip item sendiri (WAJIB pakai key)
        if ($k == $key) continue;

        // produk lain
        if (($item['type'] ?? '') === 'produk' && $item['idproduk'] == $produk->idproduk) {
            $usedStock += $item['qty'];
        }

        // paket
        if (($item['type'] ?? '') === 'paket') {
            foreach ($item['items'] as $i) {
                if ($i['idproduk'] == $produk->idproduk) {
                    $usedStock += $i['qty'] * $item['qty'];
                }
            }
        }
    }

    $currentQty = $cart[$key]['qty'] ?? 0;
    $nextQty = $currentQty + 1;

    // CEK STOK BENER (INI YANG FIX UTAMA)
    if (($usedStock + $nextQty) > $produk->stok_pusat) {
        return response()->json([
            'error' => 'Stok tidak mencukupi',
            'cart'  => $cart
        ], 422);
    }

    $cart[$key] = [
        'type' => 'produk',
        'idproduk' => $produk->idproduk,
        'nama' => $produk->nama_produk,
        'harga' => $produk->harga,
        'qty' => $nextQty,
        'max' => max(0, $produk->stok_pusat - $usedStock - $currentQty)
    ];

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
   public function updatePusat(Request $request)
{
    $request->validate([
        'idproduk' => 'required|exists:produk,idproduk',
        'qty' => 'required|integer|min:0'
    ]);

    $produk = Produk::findOrFail($request->idproduk);

    $cart = session('cart', []);
    $key = 'produk_'.$produk->idproduk;

    if (!isset($cart[$key])) {
        return response()->json(['cart' => $cart]);
    }

    $usedStock = 0;

    foreach ($cart as $k => $item) {

        if ($k == $key) continue;

        if (($item['type'] ?? '') === 'produk' && $item['idproduk'] == $produk->idproduk) {
            $usedStock += $item['qty'];
        }

        if (($item['type'] ?? '') === 'paket') {
            foreach ($item['items'] as $i) {
                if ($i['idproduk'] == $produk->idproduk) {
                    $usedStock += $i['qty'] * $item['qty'];
                }
            }
        }
    }

    if ($request->qty <= 0) {
        unset($cart[$key]);
        session(['cart' => $cart]);
        return response()->json(['cart' => $cart]);
    }

    // cek stok real
    if (($usedStock + $request->qty) > $produk->stok_pusat) {
        $cart[$key]['qty'] = max(0, $produk->stok_pusat - $usedStock);

        session(['cart' => $cart]);

        return response()->json([
            'error' => 'Stok tidak cukup',
            'max' => $produk->stok_pusat - $usedStock,
            'cart' => $cart
        ]);
    }

    $cart[$key]['qty'] = $request->qty;

    session(['cart' => $cart]);

    return response()->json(['cart' => $cart]);
}
    public function deletePusat(Request $request)
{
    $cart = session('cart', []);
    unset($cart['produk_'.$request->idproduk]);

    session(['cart'=>$cart]);

    return response()->json(['cart'=>$cart]);
}
    /* ================= PAKET Pusat ================= */
   public function addPaketPusat(Request $request)
{
    $paket = Paket::with('detail.produk')->findOrFail($request->paket_id);

    $cart = session('cart', []);
    $key = 'paket_'.$paket->id;

    $maxPaket = PHP_INT_MAX;

    foreach ($paket->detail as $d) {

        $produk = $d->produk;
        if (!$produk || $d->qty <= 0) continue;

        $usedStock = 0;

        foreach ($cart as $item) {

            if (($item['type'] ?? '') === 'produk' && $item['idproduk'] == $produk->idproduk) {
                $usedStock += $item['qty'];
            }

            if (($item['type'] ?? '') === 'paket') {
                foreach ($item['items'] as $i) {
                    if ($i['idproduk'] == $produk->idproduk) {
                        $usedStock += $i['qty'] * $item['qty'];
                    }
                }
            }
        }

        $available = $produk->stok_pusat - $usedStock;
        $maxItem = floor($available / $d->qty);

        $maxPaket = min($maxPaket, $maxItem);
    }

    if ($maxPaket <= 0) {
        return response()->json([
            'error'=>'Stok tidak cukup untuk paket',
            'cart'=>$cart
        ],422);
    }

    // BUILD ITEMS (FIX TOTAL)
    $items = [];
    foreach ($paket->detail as $d) {
        if (!$d->produk) continue;

        $items[] = [
            'idproduk' => $d->produk->idproduk,
            'nama' => $d->produk->nama_produk,
            'qty'  => $d->qty,
            'harga'=> $d->produk->harga
        ];
    }

    if (isset($cart[$key])) {

        $newQty = $cart[$key]['qty'] + 1;

        if ($newQty > $maxPaket) {
            return response()->json([
                'error'=>'Melebihi stok paket',
                'max'=>$maxPaket,
                'cart'=>$cart
            ],422);
        }

        $cart[$key]['qty'] = $newQty;
        $cart[$key]['max'] = $maxPaket;

    } else {

        $cart[$key] = [
            'type' => 'paket',
            'paket_id' => $paket->id,
            'nama' => $paket->nama_paket,
            'harga' => $paket->harga_paket,
            'qty' => 1,
            'max' => $maxPaket,
            'items' => $items
        ];
    }

    session(['cart'=>$cart]);

    return response()->json(['cart'=>$cart]);
}
public function updatePaketPusat(Request $request)
{
    $cart = session('cart', []);
    $key = 'paket_'.$request->paket_id;

    if (!isset($cart[$key])) {
        return response()->json(['cart'=>$cart]);
    }

    $paket = Paket::with('detail.produk')->findOrFail($request->paket_id);

    if ($request->qty <= 0) {
        unset($cart[$key]);
        session(['cart'=>$cart]);
        return response()->json(['cart'=>$cart]);
    }

    $maxPaket = PHP_INT_MAX;

    foreach ($paket->detail as $d) {

        $produk = $d->produk;
        if (!$produk || $d->qty <= 0) continue;

        $usedStock = 0;

        foreach ($cart as $k => $item) {

            if ($k == $key) continue;

            if (($item['type'] ?? '') === 'produk' && $item['idproduk'] == $produk->idproduk) {
                $usedStock += $item['qty'];
            }

            if (($item['type'] ?? '') === 'paket') {
                foreach ($item['items'] as $i) {
                    if ($i['idproduk'] == $produk->idproduk) {
                        $usedStock += $i['qty'] * $item['qty'];
                    }
                }
            }
        }

        $available = $produk->stok_pusat - $usedStock;
        $maxItem = floor($available / $d->qty);

        $maxPaket = min($maxPaket, $maxItem);
    }

    if ($request->qty > $maxPaket) {
        $cart[$key]['qty'] = $maxPaket;
        $cart[$key]['max'] = $maxPaket;

        session(['cart'=>$cart]);

        return response()->json([
            'error'=>'Melebihi stok paket',
            'max'=>$maxPaket,
            'cart'=>$cart
        ]);
    }

    $cart[$key]['qty'] = $request->qty;
    $cart[$key]['max'] = $maxPaket;

    session(['cart'=>$cart]);

    return response()->json(['cart'=>$cart]);
}
public function deletePaketPusat(Request $request)
{
    $cart = session('cart', []);
    unset($cart['paket_'.$request->paket_id]);

    session(['cart'=>$cart]);

    return response()->json(['cart'=>$cart]);
}
}