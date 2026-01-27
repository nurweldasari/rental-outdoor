<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanProduk;
use App\Models\DistribusiProduk;
use App\Models\Produk;

class DistribusiProdukController extends Controller
{
    /**
     * OWNER
     * Melihat semua permintaan cabang
     */
    public function index()
{
    $permintaan = PermintaanProduk::with(['produk','adminCabang.user','cabang'])
        ->orderBy('tanggal_permintaan','asc')
        ->get()
        ->groupBy('idpermintaan'); // 🔹 setiap permintaan jadi satu grup

    return view('distribusi_produk', compact('permintaan'));
}


    /**
     * OWNER
     * Setujui & kirim produk ke cabang
     */
    public function kirimPermintaan(Request $request)
{
    $request->validate([
        'jumlah_dikirim' => 'required|array',
        'jumlah_dikirim.*' => 'integer|min:0'
    ]);

    foreach ($request->jumlah_dikirim as $permintaanId => $jumlahKirim) {
        $permintaan = PermintaanProduk::findOrFail($permintaanId);
        $produk = Produk::findOrFail($permintaan->produk_idproduk);

        // Pastikan jumlah kirim <= jumlah diminta dan stok pusat
        $jumlahKirim = min($jumlahKirim, $permintaan->jumlah_diminta, $produk->stok_pusat);

        if ($jumlahKirim <= 0) continue;

        // Simpan distribusi
        DistribusiProduk::create([
            'permintaan_id'      => $permintaan->idpermintaan,
            'tanggal_distribusi' => now()->format('Y-m-d'),
            'jumlah_dikirim'     => $jumlahKirim,
            'status_distribusi'  => 'dikirim'
        ]);

        // Kurangi stok pusat
        $produk->stok_pusat -= $jumlahKirim;
        $produk->save();

        // Update status permintaan
        $permintaan->status = 'disetujui';
        $permintaan->save();
    }

    return redirect()->back()->with('success', 'Semua produk berhasil disetujui dan dikirim.');
}

public function terima($id)
{
    // ambil semua distribusi dari permintaan
    $distribusiList = DistribusiProduk::where('permintaan_id', $id)->get();

    foreach ($distribusiList as $distribusi) {
        $distribusi->confirm(); // update status & tambah stok cabang
    }

    // update status permintaan menjadi 'sampai'
    $permintaan = PermintaanProduk::findOrFail($id);
    $permintaan->status = 'sampai';
    $permintaan->save();

    return back()->with('success', 'Barang berhasil diterima dan stok cabang diperbarui.');
}

}
