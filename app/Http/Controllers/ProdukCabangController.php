<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\StokCabang;
use App\Models\Kategori;
use App\Models\Paket;
use Illuminate\Support\Facades\Auth;

class ProdukCabangController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->adminCabang) {
            abort(403, 'User tidak terhubung ke cabang');
        }

        $cabangId = $user->adminCabang->cabang_idcabang;

        $query = Produk::with([
            'kategori',
            'stokCabang' => function ($q) use ($cabangId) {
                $q->where('cabang_idcabang', $cabangId);
            }
        ])
        ->whereHas('stokCabang', function ($q) use ($cabangId) {
            $q->where('cabang_idcabang', $cabangId)
              ->where('jumlah', '>', 0);
        });

        // filter kategori
        if ($request->kategori) {
            $query->where(
                'kategori_idkategori',
                $request->kategori
            );
        }

        // search
        if ($request->search) {
            $query->where(
                'nama_produk',
                'like',
                '%'.$request->search.'%'
            );
        }

        $produkList = $query->paginate(12);
        $kategoriList = Kategori::all();


$paketList = Paket::where('cabang_id', $cabangId)
    ->with('detail.stokCabang.produk')
    ->get();
        return view(
            'produk_cabang',
            compact('produkList', 'kategoriList', 'paketList')
        );
    }

    // TOGGLE AKTIF / NONAKTIF
    // ===============================
    public function toggleStatus($idstok)
    {
        $stok = StokCabang::findOrFail($idstok);

        // 🔥 INI TARUH DI SINI
        $stok->is_active = !$stok->is_active;
        $stok->save();

        return back()->with(
            'success',
            'Status produk berhasil diubah'
        );
    }
}
