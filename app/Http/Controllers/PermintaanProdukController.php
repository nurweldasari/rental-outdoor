<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanProduk;
use App\Models\Produk;
use App\Models\AdminCabang;
use App\Models\Permintaan; // tambahkan di atas
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PermintaanProdukController extends Controller
{
    /**
     * FORM PERMINTAAN CABANG
     */
    public function create()
    {
         $userId = auth()->user()->idusers;

    // hanya admin cabang
    $isAdminCabang = AdminCabang::where('users_idusers', $userId)->exists();

    if (!$isAdminCabang) {
        abort(403, 'USER BUKAN ADMIN CABANG');
    }
        $produkList = Produk::all();
        return view('permintaan_alat', compact('produkList'));
    }

    /**
     * SIMPAN PERMINTAAN PRODUK
     */
    
public function store(Request $request)
{
    $request->validate([
        'produk_id' => 'required|array',
        'produk_id.*' => 'required|exists:produk,idproduk',
        'jumlah_diminta' => 'required|array',
        'jumlah_diminta.*' => 'required|integer|min:1',
        'keterangan' => 'nullable|string|max:255',
    ]);

    $userId = Auth::user()->idusers;
    $adminCabang = AdminCabang::where('users_idusers', $userId)->first();
    if (!$adminCabang) {
        return back()->with('error', 'Akun ini bukan admin cabang.');
    }

    DB::beginTransaction();
    try {
        // 1️⃣ Simpan header permintaan
        $permintaan = Permintaan::create([
            'cabang_idcabang'    => $adminCabang->cabang_idcabang,
            'tanggal_permintaan' => now()->format('Y-m-d'),
            'status'             => 'menunggu',
            'keterangan'         => $request->keterangan,
        ]);

        // 2️⃣ Simpan detail produk
        foreach ($request->produk_id as $i => $produkId) {
            PermintaanProduk::create([
                'permintaan_id'   => $permintaan->idpermintaan,
                'produk_idproduk' => $produkId,
                'jumlah_diminta'  => $request->jumlah_diminta[$i],
            ]);
        }

        DB::commit();
        return redirect()->route('data_permintaan')
            ->with('success', 'Permintaan produk berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}


public function riwayat(Request $request)
{
    $adminCabang = AdminCabang::where('users_idusers', Auth::user()->idusers)->first();

    if (!$adminCabang) {
        abort(403, 'Anda bukan admin cabang');
    }

    $perPage = $request->get('per_page', 10);

    $query = Permintaan::with('produkDetail.produk')
        ->where('cabang_idcabang', $adminCabang->cabang_idcabang);

    // optional search dari backend (tidak ganggu JS)
    if ($request->search) {
        $query->where('tanggal_permintaan', 'like', '%' . $request->search . '%');
    }

    $permintaan = $query
        ->orderBy('idpermintaan', 'desc')
        ->paginate($perPage)
        ->withQueryString();

    return view('data_permintaan', compact('permintaan'));
}
}