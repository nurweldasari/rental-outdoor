<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanProduk;
use App\Models\Produk;
use App\Models\AdminCabang;
use Illuminate\Support\Facades\Auth;

class PermintaanProdukController extends Controller
{
    /**
     * FORM PERMINTAAN CABANG
     */
    public function create()
    {
        $produkList = Produk::all();
        return view('permintaan_alat', compact('produkList'));
    }

    /**
     * SIMPAN PERMINTAAN PRODUK
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id'        => 'required|array',
            'produk_id.*'      => 'required|exists:produk,idproduk',
            'jumlah_diminta'   => 'required|array',
            'jumlah_diminta.*' => 'required|integer|min:1',
            'keterangan'       => 'nullable|string|max:255',
        ]);

        // ambil user login
        $userId = Auth::user()->idusers;

        // ambil admin cabang
        $adminCabang = AdminCabang::where(
            'users_idusers',
            $userId
        )->first();

        if (!$adminCabang) {
            return back()->with('error', 'Akun ini bukan admin cabang.');
        }

        foreach ($request->produk_id as $i => $produkId) {
            PermintaanProduk::create([
                'cabang_idcabang'    => $adminCabang->cabang_idcabang,
                'produk_idproduk'    => $produkId,
                'jumlah_diminta'     => $request->jumlah_diminta[$i],
                'tanggal_permintaan' => now()->format('Y-m-d'),
                'status'             => 'menunggu',
                'keterangan'         => $request->keterangan
            ]);
        }

        return redirect()
            ->route('data_permintaan')
            ->with('success', 'Permintaan produk berhasil disimpan.');
    }

    /**
     * RIWAYAT PERMINTAAN CABANG
     */
   public function riwayat()
{
    $adminCabang = AdminCabang::where(
        'users_idusers',
        Auth::user()->idusers
    )->first();

    if (!$adminCabang) {
        abort(403, 'Anda bukan admin cabang');
    }

    // Ambil semua permintaan cabang
    // dengan relasi produk dan distribusi
    $permintaan = PermintaanProduk::with(['produk', 'distribusi'])
        ->where('cabang_idcabang', $adminCabang->cabang_idcabang)
        ->orderBy('idpermintaan', 'desc') // urut permintaan terbaru
        ->get()
        ->groupBy('idpermintaan'); // 🔹 setiap permintaan jadi satu group

    return view('data_permintaan', compact('permintaan'));
}
}