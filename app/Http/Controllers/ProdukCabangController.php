<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\StokCabang;
use Illuminate\Support\Facades\Auth;

class ProdukCabangController extends Controller
{
    public function index(Request $request)
    {
        $cabangId = Auth::user()->cabang_idcabang ?? null; // asumsi user punya cabang_idcabang

        // Ambil produk yang ada stok di cabang ini
        $query = Produk::with(['kategori', 'stokCabang' => function($q) use($cabangId) {
            $q->where('cabang_idcabang', $cabangId);
        }])
        ->whereHas('stokCabang', function($q) use($cabangId) {
            $q->where('cabang_idcabang', $cabangId)
              ->where('jumlah', '>', 0); // hanya yang tersedia
        });

        if ($request->kategori) {
            $query->where('kategori_idkategori', $request->kategori);
        }

        if ($request->search) {
            $query->where('nama_produk', 'like', '%'.$request->search.'%');
        }

        $produkList = $query->paginate(20);
        $kategoriList = Kategori::all();

        return view('produk_cabang', compact('produkList', 'kategoriList'));
    }
}
