<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\AdminCabang;
use Illuminate\Support\Facades\Auth;

class ProdukCabangController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 ambil admin cabang dari user login
        $adminCabang = AdminCabang::where(
            'users_idusers',
            Auth::user()->idusers
        )->first();

        if (!$adminCabang) {
            abort(403, 'Akun bukan admin cabang');
        }

        // 🔹 ambil semua produk, relasi stokCabang untuk cabang login
        $produkList = Produk::with([
            'kategori',
            'stokCabang' => function ($q) use ($adminCabang) {
                $q->where('cabang_idcabang', $adminCabang->cabang_idcabang);
            }
        ])
        ->when($request->kategori, function($q) use ($request) {
            $q->where('kategori_id', $request->kategori);
        })
        ->when($request->search, function($q) use ($request) {
            $q->where('nama_produk', 'like', '%'.$request->search.'%');
        })
        ->get();

        $kategoriList = Kategori::all();

        return view('produk_cabang', compact(
            'produkList',
            'kategoriList',
            'adminCabang'
        ));
    }
}
