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
    // HANYA OWNER
    if(auth()->user()->status != 'owner'){
        abort(403,'Akses ditolak');
    }

    $view = $request->get('view', 'permintaan');

    // ================= PERMINTAAN =================
    $permintaan = Permintaan::whereIn('status', ['menunggu','disetujui'])
        ->with([
            'produkDetail.produk',
            'produkDetail.distribusi',
            'cabang',
            'adminCabang.user'
        ])
        ->get();
    // ================= RIWAYAT =================
    $riwayat = Permintaan::where('status','sampai')
    ->with(['produkDetail.produk', 'cabang', 'adminCabang.user'])
    ->latest()
    ->paginate(10);

    return view('distribusi_produk', compact(
        'permintaan',
        'riwayat',
        'view',
    ));
}

    // Kirim produk ke cabang
    public function kirimPermintaan(Request $request)
{
    $request->validate([
        'jumlah_dikirim' => 'required|array',
        'jumlah_dikirim.*' => 'required|integer|min:1',
        'keterangan' => 'nullable|string|max:255'
    ]);

    // ===================== VALIDASI SEMUA DULU =====================
    foreach ($request->jumlah_dikirim as $permintaanProdukId => $jumlahKirim) {

        $permintaanProduk = PermintaanProduk::findOrFail($permintaanProdukId);
        $produk = Produk::findOrFail($permintaanProduk->produk_idproduk);

        if ($jumlahKirim < 1) {
            return back()->with('error', 'Jumlah kirim tidak boleh 0.');
        }

        if ($jumlahKirim > $permintaanProduk->jumlah_diminta) {
            return back()->with('error', 'Tidak boleh melebihi jumlah permintaan.');
        }

        if ($produk->stok_pusat < $jumlahKirim) {
            return back()->with('error', 'Stok pusat tidak mencukupi. Sisa stok: ' . $produk->stok_pusat);
        }
    }

    // ===================== PROSES SIMPAN =====================
    foreach ($request->jumlah_dikirim as $permintaanProdukId => $jumlahKirim) {

        $permintaanProduk = PermintaanProduk::findOrFail($permintaanProdukId);
        $produk = Produk::findOrFail($permintaanProduk->produk_idproduk);

        DistribusiProduk::create([
            'permintaan_produk_id' => $permintaanProduk->id,
            'tanggal_distribusi'   => now(),
            'jumlah_dikirim'       => $jumlahKirim,
            'keterangan'           => $request->keterangan,
            'status_distribusi'    => 'dikirim'
        ]);

        $produk->stok_pusat -= $jumlahKirim;
        $produk->save();
    }

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
