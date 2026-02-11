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
  public function index(Request $request)
{
    $view = $request->get('view', 'permintaan'); 
    // default = permintaan

    $permintaan = Permintaan::whereIn('status', ['menunggu','disetujui'])
                    ->with(['produkPermintaan.produk','cabang','adminCabang.user'])
                    ->get();

    $riwayat = Permintaan::where('status','sampai')
                    ->with(['produkPermintaan.produk','cabang'])
                    ->get();

    return view('distribusi_produk', compact(
        'permintaan',
        'riwayat',
        'view'   
    ));
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
    DB::transaction(function () use ($id) {

        $distribusiList = DistribusiProduk::whereIn(
            'permintaan_produk_id',
            PermintaanProduk::where('permintaan_id', $id)->pluck('id')
        )->get();

        foreach ($distribusiList as $distribusi) {

            if ($distribusi->status_distribusi === 'diterima') {
                continue;
            }

            //AMBIL RELASI DETAIL
            $permintaanProduk = $distribusi->permintaanProduk;

            //AMBIL HEADER PERMINTAAN
            $permintaan = $permintaanProduk->permintaan;

            $cabangId = $permintaan->cabang_idcabang;
            $produkId = $permintaanProduk->produk_idproduk;

            //SIMPAN KE STOK CABANG
            $stokCabang = StokCabang::firstOrCreate(
                [
                    'cabang_idcabang' => $cabangId,
                    'produk_idproduk' => $produkId
                ],
                [
                    'jumlah' => 0
                ]
            );

            $stokCabang->jumlah += $distribusi->jumlah_dikirim;
            $stokCabang->save();

            // update status distribusi
            $distribusi->status_distribusi = 'diterima';
            $distribusi->save();
        }

        // update header
        Permintaan::where('idpermintaan', $id)
            ->update(['status' => 'sampai']);
    });

    return back()->with('success', 'Barang diterima & stok cabang bertambah');
}

}
