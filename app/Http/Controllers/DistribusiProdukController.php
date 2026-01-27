<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permintaan;
use App\Models\PermintaanProduk;
use App\Models\DistribusiProduk;
use App\Models\Produk;
use App\Models\StokCabang;
use Illuminate\Support\Facades\DB;

class DistribusiProdukController extends Controller
{
    // Tampilkan semua permintaan
    public function index()
    {
        $permintaan = Permintaan::with([
            'produkPermintaan.produk',
            'cabang',
            'adminCabang.user'
        ])->orderBy('tanggal_permintaan', 'asc')->get();

        return view('distribusi_produk', compact('permintaan'));
    }

    // Kirim produk ke cabang
    public function kirimPermintaan(Request $request)
    {
        $request->validate([
            'jumlah_dikirim' => 'required|array',
            'jumlah_dikirim.*' => 'integer|min:0'
        ]);

        foreach ($request->jumlah_dikirim as $permintaanProdukId => $jumlahKirim) {
            $permintaanProduk = PermintaanProduk::findOrFail($permintaanProdukId);
            $produk = Produk::findOrFail($permintaanProduk->produk_idproduk);

            $jumlahKirim = min($jumlahKirim, $permintaanProduk->jumlah_diminta, $produk->stok_pusat);
            if ($jumlahKirim <= 0) continue;

            DistribusiProduk::create([
                'permintaan_produk_id' => $permintaanProduk->id,
                'tanggal_distribusi'   => now()->format('Y-m-d'),
                'jumlah_dikirim'       => $jumlahKirim,
                'status_distribusi'    => 'dikirim'
            ]);

            $produk->stok_pusat -= $jumlahKirim;
            $produk->save();
        }

        // Update status header permintaan jadi disetujui
        $permintaanHeaderIds = PermintaanProduk::whereIn('id', array_keys($request->jumlah_dikirim))
            ->pluck('permintaan_id')
            ->unique();

        Permintaan::whereIn('idpermintaan', $permintaanHeaderIds)
            ->update(['status' => 'disetujui']);

        return back()->with('success', 'Semua produk berhasil disetujui dan dikirim.');
    }

    // Terima semua distribusi dari satu permintaan
    public function terima($id)
{
    DB::transaction(function() use ($id) {
        $distribusiList = DistribusiProduk::whereIn('permintaan_produk_id', function($q) use($id) {
            $q->select('id')
              ->from('permintaan_produk')
              ->where('permintaan_id', $id);
        })->get();

        foreach ($distribusiList as $distribusi) {
            if ($distribusi->status_distribusi == 'diterima') continue;

            $distribusi->status_distribusi = 'diterima';
            $distribusi->save();

            $cabangId = $distribusi->permintaanProduk->cabang_idcabang ?? null;
            $produkId = $distribusi->permintaanProduk->produk_idproduk ?? null;

            if ($cabangId && $produkId) {
                $stokCabang = StokCabang::firstOrCreate(
                    [
                        'cabang_idcabang' => $cabangId,
                        'produk_idproduk' => $produkId
                    ],
                    ['jumlah' => 0]
                );

                $stokCabang->jumlah += $distribusi->jumlah_dikirim;
                $stokCabang->save();
            }
        }

        $permintaanHeader = Permintaan::findOrFail($id);
        $permintaanHeader->status = 'sampai';
        $permintaanHeader->save();
    });

    return back()->with('success', 'Barang berhasil diterima dan stok cabang diperbarui.');
}

}
